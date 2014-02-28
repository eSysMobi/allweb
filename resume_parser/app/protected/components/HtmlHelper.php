<?
class HtmlHelper extends CComponent
{
    public static function getItem($html,$selector,$num=0,$content='innertext')
    {
		if ($html) {
			$got = $html->find($selector,$num);
			if ($got) {
				return trim($got->$content);
			}
		}
		return null;
    }
	public static function getItems($html,$selector) {
		$got = $html->find($selector);
		if ($got && !empty($got)) {
			return $got;
		} else {
			return null;
		}
	}
	public static function findContains($html,$selector,$contains,$out='first') {
		$items = $html->find($selector);
		if ($out=='all') {
			$all = array();
		}
		if (!empty($items)) {
			foreach($items as $item) {
				if(strpos($item->innertext, $contains) !== false) {
					if ($out=='first') {
						return $item;
					}
					$all[] = $item;
				}
			}
		}
		if ($out=='all') {
			return $all;
		}
		return null;
	}
	public static function loadHtml($url) {
		$cnt=0;	
		while($cnt < 3 && ($result=@file_get_contents($url))===false) {
			$cnt++;
			sleep(Settings::getOption('delay'));
		}
		if ($result===false || empty($result)) {
			Yii::log('Cannot open URL - '.($url?$url:'nolink'), 'warning', 'Parsing');
		}
		sleep(Settings::getOption('delay'));
		$html = str_get_html($result);
		return $html;
	}
}