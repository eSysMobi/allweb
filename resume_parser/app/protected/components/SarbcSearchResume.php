<?
class SarbcSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://job.sarbc.ru';
	public $site='sarbc';
	
	public $name;
	public $exp;
	public $age;
	public $edu;
	public $worktype;
	public $description;
	public $phone;
	public $email;
	public function load_from_short_html(&$html) {
		$uls = HtmlHelper::getItems($html,'ul li');
		$date = $uls[1]->innertext;
		$date = explode('</strong>: ',$date);
		$date = $date[1];
		$this->creation_date = $this->format_date($date);
		$salary = $uls[2]->innertext;
		$salary = explode('</strong>: ',$salary);
		$salary = UtilityHelper::cleanString($salary[1]);
		if (strpos($salary,'указана') === false) {
			$salary = explode(' ',$salary);
			array_shift($salary);
			$this->salary = implode(' ',$salary).' р.';
		} else {
			$this->salary = null;
		}
		$this->job = mb_convert_encoding(HtmlHelper::getItem($html,'a[href*=resume]',0,'innertext'), "utf-8", "windows-1251");
		$this->link .= HtmlHelper::getItem($html,'a[href*=resume]',0,'href');
	}
	
	private function format_date($date) {
		$exploded = explode('.',$date);
		return($exploded[2].'-'.$exploded[1].'-'.$exploded[0].' 00:00:00');
	}
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		$wrapper = array_shift(HtmlHelper::getItems($html,'div.sectionList'));
		$this->name = $this->parse_li($wrapper,'Контактное лицо');
		$this->phone = UtilityHelper::formatPhone($this->parse_li($wrapper,'Телефон'));
		$this->email = $this->parse_li($wrapper,'E-mail');
		$this->age = $this->parse_li($wrapper,'Возраст');
		$this->edu = $this->parse_li($wrapper,'Образование');
		$this->exp = $this->parse_li($wrapper,'Опыт работы');
		$this->worktype = $this->parse_li($wrapper,'График работы');
		$exploded_section = explode('</div>',$wrapper->innertext);
		$exploded_exploded_section = explode('<div class="title">',$exploded_section[1]);
		$this->description = $exploded_exploded_section[0];
		$html->clear();
		unset($html);
		$wrapper->clear();
		unset($wrapper);
		
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
		$model->description=(($this->worktype)?'Тип - '.$this->worktype.'. ':'').
		($this->age?'Возраст - '.$this->age.'. ':'').($this->edu?'Образование - '.$this->edu.'. ':'').($this->exp?'Опыт - '.$this->exp.'. ':'').($this->description?$this->description:'');
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
	private function parse_li($wrapper,$str,$all=false) {
		$li = HtmlHelper::findContains($wrapper,'li',$str,'first');
		if ($li) {
			$li = $li->plaintext;
			$out = explode(': ',$li);
			if ($all) {
				return $out;
			} else {
				return $out[1];
			}
		}
		return null;
	}
}