<?
class AvitoSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $district;
	public $creation_date;
	public $link;
	public $site='avito';
	
	public $worktype;
	public $category;
	public $name;
	public $description;
	public $phone;
	public $phone_link;
	public function load_from_short_html($html) {
		$this->job = HtmlHelper::getItem($html,'h3.item-header');
		$this->salary = HtmlHelper::getItem($html,'div.item-price');
		$this->salary = ($this->salary=='з/п не указанa')?null:$this->salary;
		$this->district = HtmlHelper::getItem($html,'div.item-info span.info-text');
		$this->creation_date = $this->format_date(HtmlHelper::getItem($html,'div.item-info div.info-date'));
		$this->link = 'http://m.avito.ru'.HtmlHelper::getItem($html,'a.item-link',0,'href');
	}
	
	private function format_date($date) {
		$date = str_replace('&nbsp;',' ',$date);
		$replaced = str_replace('Сегодня,',date("Y-m-d"),$date);
		if ($replaced!=$date) {
			return $replaced.':00';
		}
		$replaced = str_replace('Вчера,',date("Y-m-d", strtotime("yesterday")),$date);
		if ($replaced!=$date) {
			return $replaced.':00';
		}
		$months=array('янв','фев','мар','апр','май','июн','июл','авг','сен','окт','ноя','дек');
		$found = false;
		foreach($months as $num => $month) {
			if(strpos($date,$month) !== false) {
				$found = true;
				break;
			}
		}
		if (!$found) {
			return null;
		}
		$num++;
		$current_month = date('m');
		if ($current_month>=$num) {
			$year = date("Y");
		} else {
			$year = date("Y")-1;
		}
		$exploded_date = explode(',',$date);
		$exploded_exploded_date = explode(' ',$exploded_date[0]);
		if ($num < 10) $num = '0'.$num;
		return($year.'-'.$num.'-'.$exploded_exploded_date[0].$exploded_date[1].':00');
	}
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		$this->worktype = HtmlHelper::getItem($html,'article.b-single-item section.single-item-info div.info-params span.param');
		$this->category = HtmlHelper::getItem($html,'article.b-single-item section.single-item-info div.info-params span.param',1);
		$this->name = HtmlHelper::getItem($html,'div.person-name',0,'plaintext');
		$this->name = explode(' ',$this->name);
		$this->name = $this->name[0];
		$this->description = HtmlHelper::getItem($html,'div.description-wrapper');
		$this->phone_link = 'http://m.avito.ru'.HtmlHelper::getItem($html,'li.action-show-number a',0,'href');
		$this->phone = $this->get_phone();
		$html->clear();
	}
	public function get_phone() {
		if ($this->phone_link) {
			$fail = true;
			$cnt = 1;
			while($fail && $cnt<=3) {
				if ($cnt>1) {
					sleep(Settings::getOption('delay'));
				}
				$user_agent = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)';
				$header[] = "Host: m.avito.ru";
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $this->phone_link.'?async');
				curl_setopt($ch, CURLOPT_TIMEOUT, 10);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
				curl_setopt($ch, CURLOPT_REFERER, $this->link);
				curl_setopt($ch, CURLOPT_AUTOREFERER, true);   
				curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
				curl_setopt($ch, CURLOPT_HEADER, false);
				curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
				curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
				curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
				$result = curl_exec($ch);
				curl_close($ch);
				$json_phone = json_decode($result);
				if ($json_phone && isset($json_phone->phone)) {
					return($json_phone->phone);
				}
				$cnt++;
			}
			Yii::log('Avito. Phone cannot be parsed for - '.($this->link?$this->link:'nolink'), 'warning', 'Parsing');
			return null;
		}
	}
	public function to_db($check_existance=true) {
		if ($check_existance) {
			if ($this->check_in_db()) {
				return false;
			}
		}
		$model = new Resumes;
		$model->site=$this->site;
		$model->name=$this->name;
		$model->job=$this->job;
		$model->{'date'}=$this->creation_date;
		$model->description=(($this->worktype)?'Рабочий день - '.$this->worktype.'. ':'').
		($this->category?'Отрасль - '.$this->category.'. ':'').($this->description?$this->description:'');
		$model->phone = UtilityHelper::formatPhone($this->phone);
		$model->link=$this->link;
		if($model->save()) {
			return true;
		} else {
			Yii::log(print_r($model->getErrors(), true), 'warning', 'MySQL');
		}
	}
	public function check_in_db() {
		if ($this->link) {
			$record=Resumes::model()->find(array(
				'select'=>'id',
				'condition'=>'link=:link AND site=:site',
				'params'=>array(':link'=>$this->link,
								':site'=>$this->site))
			);
			if($record===null) {
				return false;
			} else {
				return true;
			}
		}
		return false;
	}
}