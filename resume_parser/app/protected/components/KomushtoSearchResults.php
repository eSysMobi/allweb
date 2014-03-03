<?
class KomushtoSearchResults extends CComponent
{
	public $results;
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.Ru164SearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		$last=false;
		for($num=1; $num<=5; $num++) {
			$html = HtmlHelper::loadHtml('http://www.komuchto.ru/advert/search/?&advf_act=6&advf_rid=13&page='.$num);
			$results = $html->find('div.adv_show tr.w');
			echo 'Страница '.$num.'<br />';
			foreach($results as &$result) {
				$item = new KomushtoSearchResume();
				$item->load_from_short_html($result);
				$difference = UtilityHelper::days_ago($item->creation_date);
				if (!($difference<Settings::getOption('parse_days'))) {
					$last = true;
					break;
				}
				if (!$item->check_in_db()) {
					$item->load_full();
					$item->to_db(false);
					$this->results[] = $item;
					echo 'Объявление '.$item->job.' '.round($difference,2).'<br />';
				}
			}
			if($last) {
				break;
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