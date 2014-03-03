<?
class Ru164SearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link;
	public $site='164ru';
	public $name;
	
	public $age;
	public $district;
	public $worktype;
	public $description;
	public $phone;
	public $email;
	public function load_from_short_html(&$html) {
		$tds = HtmlHelper::getItems($html,'td');
		if (!empty($tds)) {
			// echo $new_string;
			$this->creation_date = $this->format_date($tds[0]->plaintext);
			$this->name = UtilityHelper::cleanString($tds[1]->plaintext);
			$this->job = UtilityHelper::cleanString($tds[2]->find('span',0)->plaintext);
			$salary_exploded = explode(' ',trim($tds[3]->plaintext));
			if (strpos($salary_exploded[0],'Договорная') === false) {
				array_shift($salary_exploded);
				$this->salary = implode(' ',$salary_exploded).' р.';
			} else {
				$this->salary = null;
			}
			
			$this->link=$html->find('a.j_link_list',0)->href;
		}
		unset($tds);
	}
	
	private function format_date($date) {
		$date = UtilityHelper::cleanString($date);
		foreach(array(array('сегодня',false),array('позавчера',strtotime("-2 day")),array('вчера',strtotime("yesterday"))) as $time_word) {
			if (strpos($date,$time_word[0]) !== false) {
				$reversed = implode(' ',array_reverse(explode(' ',$date)));
				if (!$time_word[1]) {
					return str_replace($time_word[0],date("Y-m-d"),$reversed).':00';
				} else {
					return str_replace($time_word[0],date("Y-m-d", $time_word[1]),$reversed).':00';
				}
			}
		}
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
		$current_month = date('m');
		if ($current_month>=$num) {
			$year = date("Y");
		} else {
			$year = date("Y")-1;
		}
		if ($num < 10) $num = '0'.$num;
		return($year.'-'.$num.'-'.$exploded[1].' '.$exploded[0].':00');
	}
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		echo '<br />'.$this->link.'<br />';
		$tables = HtmlHelper::getItems($html,'#block_center table table');
		$personal_info_table = $tables[2];
		$this->age = HtmlHelper::getItem(HtmlHelper::findContains($personal_info_table,'tr','Возраст'),'td',1);
		$contacts_table = $tables[3];
		$this->district = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','Район'),'td',1), "utf-8", "windows-1251");
		$this->phone = UtilityHelper::formatPhone(HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','Телефон'),'td',1));
		$this->email = HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','mail'),'td',1,'plaintext');
		$wishes_table = $tables[4];
		$this->worktype = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($wishes_table,'tr','График работы'),'td',1), "utf-8", "windows-1251").' '.mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($wishes_table,'tr','Тип работы'),'td',1), "utf-8", "windows-1251");
		$exp_table = $tables[5];
		$this->description = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($exp_table,'tr','Профессиональные навыки'),'td',1), "utf-8", "windows-1251");
		if ($html) {
			$html->clear();
		}
		unset($html);
		$personal_info_table->clear();
		unset($personal_info_table);
		$contacts_table->clear();
		unset($contacts_table);
		$wishes_table->clear();
		unset($wishes_table);
		$exp_table->clear();
		unset($exp_table);
		unset($tables);
		
		
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
		($this->age?'Возраст - '.$this->age.'. ':'').($this->description?$this->description:'');
		$model->phone = $this->phone;
		$model->email = $this->email;
		$model->link=$this->link;
		$model->salary=str_replace('&nbsp;',' ',$this->salary);
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