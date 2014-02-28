<?
class Ru164SearchResults extends CComponent
{
	public $results;
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.Ru164SearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		$last=false;
		for($num=1; $num<=7; $num++) {
			echo 'Страница '.$num.'<br />';
			$html = HtmlHelper::loadHtml('http://164.ru/job/resume/'.$num.'.php?firstPage=0');
			$results = $html->find('table.table2 tr[id*=row]');
			
			 if(!empty($this->results)) {
				 $difference = UtilityHelper::days_ago(end($this->results)->creation_date);
				 if (!($difference<Settings::getOption('parse_days'))) {
					 $last = true;
					 break;
				 }
			 }
			foreach($results as $result) {
				$item = new Ru164SearchResume();
				$item->load_from_short_html($result);
				$difference = UtilityHelper::days_ago($item->creation_date);
				if (!$item->check_in_db()) {
					$item->load_full();
					$item->to_db(false);
					$this->results[] = $item;
					echo 'Объявление '.$item->job.' '.round($difference,2).'<br />';
				}
			}
			if ($num==3) {
				die;
			}
			$html->clear();
		}
	}
	public function results_to_db() {
		if(!empty($this->results)) {
			foreach($this->results as &$result) {
				$result->to_db(); 
			}
		}
	}
}