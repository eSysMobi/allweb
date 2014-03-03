<?
class RdwSearchResults extends CComponent
{
	public $results;
	private $login = 'jana-hohlova@rambler.ru';
	private $pwd = 'NYEZwu2';
	private $name_to_search = 'Хохлова';
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.RdwSearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		$last=false;
		$html = HtmlHelper::loadHtmlFromString(HtmlHelper::curlLoad('http://saratov.rdw.ru/'));
		$form = HtmlHelper::getItem($html,'form[action="/user/auth/run"]',0,'pure');
		$input['referer'] = HtmlHelper::getItem($form,'input[name=referer]',0,'value');
		$input['__token'] = HtmlHelper::getItem($form,'input[name=__token]',0,'value');
		$input['login'] = $this->login;
		$input['pwd'] = $this->pwd;
		$input['remember_me'] = 'on';
		$html->clear();
		unset($html);
		unset($form);
		$html = mb_convert_encoding(HtmlHelper::curlPostRequest('http://saratov.rdw.ru/user/auth/run','http://saratov.rdw.ru/',$input), "utf-8", "windows-1251");
		if (strpos($html,$this->name_to_search) === false) {
			Yii::log('Cannot login.', 'warning', 'Rdw');
		}
		for($num=1; $num<=85; $num++) {
			$search_url = 'http://saratov.rdw.ru/resume/list/saratov?show=ka&page='.$num;
			$html = HtmlHelper::loadHtmlFromString(mb_convert_encoding(HtmlHelper::curlLoad($search_url), "utf-8", "windows-1251"));
			$results = $html->find('ul.search-results-list li');
			echo 'Страница '.$num.'<br />';
			foreach($results as &$result) {
				$item = new RdwSearchResume();
				$item->load_from_short_html($result);
				$item->load_full($search_url);
				$difference = UtilityHelper::days_ago($item->creation_date);
				if (!($difference<Settings::getOption('parse_days'))) {
					$last = true;
					break;
				}
				if (!$item->check_in_db()) {
					$item->to_db(false);
					$this->results[] = $item;
					echo 'Объявление '.$item->job.' '.round($difference,2).'<br />';
				}
			}
			$html->clear();
			unset($html);
			unset($results);
			if ($last) {
				break;
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