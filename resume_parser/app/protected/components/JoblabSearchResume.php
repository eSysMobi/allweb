<?
class JoblabSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://joblab.ru';
	public $host='http://joblab.ru';
	public $site='joblab';
	
	public $name;
	public $description;
	public $phone;
	public $email;
	public $phone_link;
	private $rows;
	public function load_from_short_html(&$html1,&$html2) {
		$this->creation_date = $this->format_date(HtmlHelper::getItem($html2,'div.small',0,'plaintext'));
		$this->job = HtmlHelper::getItem($html1,'span.prof',0,'plaintext');
		$this->link .= HtmlHelper::getItem($html1,'span.prof a',0,'href');
		$this->salary = HtmlHelper::getItem($html1,'td',1,'plaintext');
		if (strpos($this->salary,'договор') === false) {
			$this->salary = str_replace('от ','',$this->salary);
			$this->salary = str_replace('уб.','.',$this->salary);
		} else {
			$this->salary = null;
		}
	}
	
	private function format_date($date) {
		$date = UtilityHelper::cleanString($date);
		$replaced = str_replace('сегодня,',date("Y-m-d"),$date);
		if ($replaced!=$date) {
			return $replaced.':00';
		}
		$replaced = str_replace('вчера,',date("Y-m-d", strtotime("yesterday")),$date);
		if ($replaced!=$date) {
			return $replaced.':00';
		}
		$exploded = explode(', ',$date);
		$exploded_exploded = explode('.',$exploded[0]); 
		return($exploded_exploded[2].'-'.$exploded_exploded[1].'-'.$exploded_exploded[0].' '.$exploded[1].':00');
	}
	public function load_full($url) {
		$html = HtmlHelper::loadHtmlFromString(mb_convert_encoding(HtmlHelper::curlLoad($this->link,$url), "utf-8", "windows-1251"));
		$trs = HtmlHelper::getItems($html,'.contentmain table tr');
		$rows = array();
		foreach($trs as $tr) {
			$tds = HtmlHelper::getItems($tr,'td');
			if (count($tds)>=1.5) {
				$rows[] = array('title' => $tds[0]->plaintext,'content' => $tds[1]->plaintext,'type' => 'text');
			} else {
				$rows[] = array('title' => $tds[0]->plaintext, 'content' => null, 'type' => 'title');
			}
			unset($tds);
		}
		$this->rows = $rows;
		$this->name = $this->parse_row('Имя:');
		$this->phone = UtilityHelper::formatPhone($this->parse_row('Телефон:'));
		$this->description = $this->parse_row('Рубрика:','all').'<br />';
		$this->description .= $this->parse_row('График работы:','all').'<br />';
		$this->description .= $this->parse_row('Образование:','all').'<br />';
		$this->description .= $this->parse_row('Опыт работы:','all').'<br />';
		$this->description .= $this->parse_row('Гражданство:','all').'<br />';
		$this->description .= $this->parse_row('Возраст:','all').'<br />';
		$this->description .= $this->parse_row('Семейное положение:','all').'<br />';
		$this->description .= $this->parse_row('Образование:','all').'<br />';
		$html->clear();
		unset($html);
		unset($trs);
		
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
		$model->link=$this->link;
		$model->salary=str_replace(array('&thinsp;','&nbsp;'),' ',$this->salary);
		if($model->save()) {
			return true;
		} else {
			Yii::log(print_r($model->getErrors(), true), 'warning', 'MySQL');
		}
	}
	private function parse_row($str,$out='content') {
		foreach($this->rows as $row) {
			if (strpos($row['title'],$str)!==false) {
				if ($out=='all') {
					return $row['title'].' '.$row['content'];
				}
				return $row['content'];
			}
		}
		return null;
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