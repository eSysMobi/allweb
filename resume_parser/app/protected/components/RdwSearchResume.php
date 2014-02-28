<?
class RdwSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://saratov.rdw.ru';
	public $site='rdw';
	
	public $name;
	public $age;
	public $district;
	public $worktype;
	public $description;
	public $phone;
	public $email;
	public function load_from_short_html(&$html) {
		$this->creation_date = $this->format_date(mb_convert_encoding(HtmlHelper::getItem($html,'p.date'), "utf-8", "windows-1251"));
		$this->job = mb_convert_encoding(HtmlHelper::getItem($html,'h2',0,'plaintext'), "utf-8", "windows-1251");
		$this->link .= HtmlHelper::getItem($html,'h2 a',0,'href');
		$this->salary = mb_convert_encoding(HtmlHelper::getItem($html,'span.red_salary_txt'), "utf-8", "windows-1251");
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
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		$this->description = HtmlHelper::getItem($html,'ul.prof',0,'plaintext').'<br />';
		$this->description .= HtmlHelper::getItem($html,'dl.module-list-desc',0,'plaintext').'<br />';
		$this->description .= HtmlHelper::getItem($html,'table.vac_description',0,'plaintext');
		//Тут будут контактные данные(получение таковых)
		$contacts_table = $tables[3];
		$this->district = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','Район'),'td',1), "utf-8", "windows-1251");
		$this->phone = UtilityHelper::formatPhone(HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','Телефон'),'td',1));
		$this->email = HtmlHelper::getItem(HtmlHelper::findContains($contacts_table,'tr','mail'),'td',1,'plaintext');
		$wishes_table = $tables[4];
		$this->worktype = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($wishes_table,'tr','График работы'),'td',1), "utf-8", "windows-1251").' '.mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($wishes_table,'tr','Тип работы'),'td',1), "utf-8", "windows-1251");
		$exp_table = $tables[5];
		$this->description = mb_convert_encoding(HtmlHelper::getItem(HtmlHelper::findContains($exp_table,'tr','Профессиональные навыки'),'td',1), "utf-8", "windows-1251");
		$html->clear();
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