<?
class KomushtoSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://www.komuchto.ru';
	public $site='komushto';
	
	public $category;
	public $description;
	public $phone;
	
	
	public function load_from_short_html(&$html) {
		$url = array_shift(HtmlHelper::getItems($html,'a.adv_list'));
		$this->job = mb_convert_encoding(HtmlHelper::getItem($url,'b'), "utf-8", "windows-1251");
		$this->link .= $url->href;
		$this->creation_date = $this->format_date(HtmlHelper::getItem($html,'div[style*=10px]',0,'plaintext'));
		$url->clear();
		unset($url);
		$last_td = end(HtmlHelper::getItems($html,'td'));
		if (strpos($last_td->plaintext,'-') === false) {
			$this->salary = UtilityHelper::cleanString(str_replace('тыс.','000',$last_td->plaintext).' р.');
		} else {
			$this->salary = null;
		}
		$last_td->clear();
		unset($last_td);
	}
	
	private function format_date($date) {
		$exploded = explode('.',array_shift(explode(' ',$date)));
		return($exploded[2].'-'.$exploded[1].'-'.$exploded[0].' 00:00:00');
	}
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		$phone_tr = HtmlHelper::findContains($html,'table.advonetable table.advonetable tr','adv_phone.png');
		$this->phone = $phone_tr->plaintext;
		$phone_tr->clear();
		unset($phone_tr);
		$category_tr = HtmlHelper::getItems($html,'table.advonetable tr');
		$category_tr = $category_tr[0];
		$category_td = HtmlHelper::getItems($category_tr,'p');
		if ($category_td[1]) {
			$this->category = str_replace('Размещено: ','',mb_convert_encoding($category_td[1]->innertext, "utf-8", "windows-1251"));
		}
		$description_td = mb_convert_encoding(HtmlHelper::getItem($html,'table.advonetable tr',1,'plaintext'), "utf-8", "windows-1251");
		if ($description_td) {
			$this->description = UtilityHelper::cleanString($description_td);
		}
		$html->clear();
		unset($html);
		unset($category_tr);
		unset($category_td);
	}
	public function to_db($check_existance=true) {
		if ($check_existance) {
			if ($this->check_in_db()) {
				return false;
			}
		}
		$model = new Resumes;
		$model->site=$this->site;
		$model->job=$this->job;
		$model->{'date'}=$this->creation_date;
		$model->description=(($this->category)?'Отрасль - '.$this->category.'. ':'').($this->description?$this->description:'');
		$model->phone = $this->phone;
		$model->link=$this->link;
		$model->salary=$this->salary;
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