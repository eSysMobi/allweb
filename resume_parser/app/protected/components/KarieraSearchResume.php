<?
class KarieraSearchResume extends CComponent
{
	public $job;
	public $salary;
	public $creation_date;
	public $link='http://kariera-l.ru';
	public $site='kariera';
	
	public $description;
	public $phone;
	public $name;
	public $email;
	
	
	public function load_from_short_html(&$html) {
		$this->link .= HtmlHelper::getItem($html,'a',0,'href');
		$tds = HtmlHelper::getItems($html,'td');
		$this->job = UtilityHelper::cleanString($tds[1]->plaintext);
		$this->creation_date = $this->format_date($tds[3]->plaintext);
		if (!empty($tds[2]->plaintext)) {
			$this->salary = UtilityHelper::cleanString($tds[2]->plaintext);
		} else {
			$this->salary = null;
		}
		unset($tds);
	}
	
	private function format_date($date) {
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
		
		$exploded = explode(' ',$date);
		return($exploded[2].'-'.$num.'-'.$exploded[0].' 00:00:00');
	}
	public function load_full() {
		$html = HtmlHelper::loadHtml($this->link);
		$wrapper = str_replace(array('</h3>','</h1>'),'</b>',str_replace(array('<h1 class="head1">','<h3 class="head3">','<h3 class=head3>'),'<b>',str_replace('<hr>','<br>',HtmlHelper::getItem($html,'div.centercontentpub2'))));
		$wrapper = preg_replace_callback('/(<b>)(.*?)(<\/b>)/i',array($this, 'change_encoding_cb'),$wrapper);
		$wrapper = explode('<center>',$wrapper);
		$wrapper =  mb_convert_encoding($wrapper[0], "utf-8", "windows-1251");
		$exploded_wrapper = explode('<br>',$wrapper);
		$this->name = $this->parse_from_wrapper($exploded_wrapper,'Имя:');
		$this->phone = $this->parse_from_wrapper($exploded_wrapper,'Телефон:');
		$this->email = $this->parse_from_wrapper($exploded_wrapper,'E-mail:');
		$this->description = $wrapper;
		$this->description = explode('Сведения о соискателе',$this->description);
		if (isset($this->description[1])) {
			$this->description = $this->description[1];
		} else {
			$this->description = $wrapper;
		}
		$html->clear();
		unset($html);
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
		$model->description=$this->description;
		if ($this->phone) {
			$model->phone = $this->phone;
		}
		if ($this->phone) {
			$model->email = $this->email;
		}
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
	private function parse_from_wrapper($exploded_wrapper,$str) {
		foreach($exploded_wrapper as $part) {
			$exploded = explode('</b>',$part);
			if (count($exploded)>1.5) {
				if (strpos($exploded[0],$str) !== false) {
					if (strpos($exploded[1],'<')) {
						$exploded[1] = explode('<',$exploded[1]);
						$exploded[1] = $exploded[1][0];
					}
					return(trim($exploded[1]));
				}
			}
		}
		return null;
	}
	public function change_encoding_cb($str) {
		return mb_convert_encoding('<b>'.$str[2].'</b>', "windows-1251", "utf-8");
	}
}