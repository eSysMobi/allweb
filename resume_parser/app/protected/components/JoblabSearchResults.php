<?
class JoblabSearchResults extends CComponent
{
	public $results;
	private $login = 'jana-hohlova@rambler.ru';
	private $pwd = 'london';
	private $name_to_search = 'Хохлова';
	function __construct(){
		UtilityHelper::loadHelper('simple_html_dom');
		Yii::import('application.components.RdwSearchResume');
		Yii::import('application.components.HtmlHelper');
	}
	public function get_results() {
		$last=false;
		$html = HtmlHelper::curlLoad('http://joblab.ru/access.php');
		$input['auth_name_job'] = $this->login;
		$input['pass'] = $this->pwd;
		$input['remember_me'] = 1;
		$input['retpath'] = null;
		$input['submit'] = 'Войти';
		$input['type'] = 'employer';
		$html = mb_convert_encoding(HtmlHelper::curlPostRequest('http://joblab.ru/access.php','http://joblab.ru/access.php',$input), "utf-8", "windows-1251");
		if (strpos($html,$this->name_to_search) === false) {
			Yii::log('Cannot login.', 'warning', 'Rdw');
		}
		for($num=1; $num<=100; $num++) {
			$search_url = 'http://joblab.ru/search_resume.php?srregion=64&srcity=58&submit=1&page='.$num;
			$html = HtmlHelper::loadHtmlFromString(mb_convert_encoding(HtmlHelper::curlLoad($search_url), "utf-8", "windows-1251"));
			$results_table = HtmlHelper::getItem($html,'.contentmain table',2,'pure');
			$results = HtmlHelper::getItems($results_table,'tr');
			$results_table->clear();
			unset($results_table);
			foreach($results as $key => $result) {
				if (strpos($result->innertext,'noindex')!==false) {
					unset($results[$key]);
				}
			}
			$results = array_values($results);
			echo 'Страница '.$num.'<br />';
			for($i=0; $i<20; $i++) {
				$item = new JoblabSearchResume();
				$item->load_from_short_html($results[$i*2],$results[$i*2+1]);
				$difference = UtilityHelper::days_ago($item->creation_date);
				echo $difference.' ';
				if (!($difference<Settings::getOption('parse_days'))) {
					$last = true;
					break;
				}
				if (!$item->check_in_db()) {
					$item->load_full($search_url);
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