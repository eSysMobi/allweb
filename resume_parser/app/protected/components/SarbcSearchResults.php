<?
class SarbcSearchResults extends CComponent
{
	public $results;
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.Ru164SearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		$last=false;
		for($num=1; $num<=3; $num++) {
			$html = HtmlHelper::loadHtml('http://job.sarbc.ru/resume.html?first='.(($num-1)*30));
			$results = $html->find('div.mainContent div.sectionList');
			
			 if(!empty($this->results)) {
				 $difference = UtilityHelper::days_ago(end($this->results)->creation_date);
				 if (!($difference<Settings::getOption('parse_days'))) {
					 $last = true;
					 break;
				 }
			 }
			echo 'Страница '.$num.'<br />';
			foreach($results as &$result) {
				$item = new SarbcSearchResume();
				$item->load_from_short_html($result);
				$difference = UtilityHelper::days_ago($item->creation_date);
				if (!$item->check_in_db()) {
					$item->load_full();
					$item->to_db(false);
					$this->results[] = $item;
					echo 'Объявление '.$item->job.' '.round($difference,2).'<br />';
				}
			}
			$html->clear();
			unset($html);
			unset($results);
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