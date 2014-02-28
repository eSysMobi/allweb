<?
class HtmlHelper extends CComponent
{
    public static function getItem($html,$selector,$num=0,$content='innertext')
    {
        $got = $html->find($selector,$num);
		if ($got) {
			return trim($got->$content);
		} else {
			return null;
		}
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