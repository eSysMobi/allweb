<?
class RdwSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://saratov.rdw.ru';
	public $host='http://saratov.rdw.ru';
	public $site='rdw';
	
	public $name;
	public $description;
	public $phone;
	public $email;
	public $phone_link;
	public function load_from_short_html(&$html) {
		$this->creation_date = $this->format_date(HtmlHelper::getItem($html,'p.date'));
		$this->job = HtmlHelper::getItem($html,'h2',0,'plaintext');
		$this->link .= HtmlHelper::getItem($html,'h2 a',0,'href');
		$this->salary = HtmlHelper::getItem($html,'span.red_salary_txt');
		if (strpos($this->salary,'договор') === false) {
			$this->salary = str_replace('от ','',$this->salary);
			$this->salary = str_replace('уб','',$this->salary);
		} else {
			$this->salary = null;
		}
	}
	
	private function format_date($date) {
		$date = UtilityHelper::cleanString($date);
		$exploded = explode(' ',$date);
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
		if ($num < 10) $num = '0'.$num;
		return($exploded[2].'-'.$num.'-'.$exploded[0].' 00:00:00');
	}
	public function load_full($url) {
		$html = HtmlHelper::loadHtmlFromString(mb_convert_encoding(HtmlHelper::curlLoad($this->link,$url), "utf-8", "windows-1251"));
		$this->description = HtmlHelper::getItem($html,'ul.prof',0,'plaintext').'<br />';
		$this->description .= HtmlHelper::getItem($html,'.section-wide .module dl.module-list-desc',0,'plaintext').'<br />';
		$this->description .= HtmlHelper::getItem($html,'table.vac_description',0,'plaintext');
		$dd = HtmlHelper::getItem($html,'div.module-employer dl.module-list-desc dd',1,'pure');
		$this->name = HtmlHelper::getItem($dd,'p.break-word span',0,'plaintext');
		$contact_dd = HtmlHelper::findContains($html,'dd','c_email');
		$contact_dd_text = str_replace(array('\n','\r','\t','	',' '),'',$contact_dd->innertext);
		preg_match_all('/getJSON\\(\\\'(.*?)\\\'\\,/',$contact_dd_text, $matches);
		$this->phone_link = $this->host.$matches[1][1];
		$cnt = 1;
		while(!$this->get_contacts() && $cnt<=3) {
			$cnt++;
		}
		$html->clear();
		unset($html);
		$dd->clear();
		unset($dd);
		
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
		$model->description=($this->description?$this->description:'');
		$model->phone = $this->phone;
		$model->email = $this->email;
		$model->link=$this->link;
		$model->salary=str_replace(array('&thinsp;','&nbsp;'),' ',$this->salary);
		if($model->save()) {
			return true;
		} else {
			Yii::log(print_r($model->getErrors(), true), 'warning', 'MySQL');
		}
	}
	public function get_contacts() {
		$contacts = mb_convert_encoding(HtmlHelper::curlLoad($this->phone_link,$this->link), "utf-8", "windows-1251");
		if ($decoded = json_decode($contacts)) {
			if (!empty($decoded->EMAIL)) {
				$this->email = $decoded->EMAIL;
			}
			if (!empty($decoded->PHONE_CONTACT)) {
				$this->phone = UtilityHelper::formatPhone($decoded->PHONE_CONTACT);
			}
			return true;
		}
		return false;
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