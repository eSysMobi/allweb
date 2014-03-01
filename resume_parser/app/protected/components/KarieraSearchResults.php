<?
class KarieraSearchResults extends CComponent
{
	public $results;
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.Ru164SearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		for($catid=1; $catid<=36; $catid++) {
			if ($catid!=23) {
				echo 'Категория '.$catid.'<br />';
				$last=false;
				for ($page=0; $page<=5; $page++) {
					if (isset($html)) {
						$html->clear();
						unset($html);
					}
					$html = HtmlHelper::loadHtml('http://kariera-l.ru/?k=2&c=1&nb=0&page='.($page*30).'&r='.$catid.'&time=1');
					$results = $html->find('table.index tr.content61');

					if (!empty($results)) {
						foreach($results as &$result) {
							$item = new KarieraSearchResume();
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
						unset($results);
						if ($last) {
							break;
						}
					} else {
						break;
					}
				}
			}
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