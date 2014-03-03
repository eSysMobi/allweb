<?
class HtmlHelper extends CComponent
{
    public static function getItem($html,$selector,$num=0,$content='innertext')
    {
		if ($html) {
			$got = $html->find($selector,$num);
			if ($got) {
				if ($content=='pure') {
					return $got;
				} else {
					return trim($got->$content);
				}
			}
		}
		return null;
    }
	public static function getItems($html,$selector) {
		if ($html) {
			$got = $html->find($selector);
			if ($got && !empty($got)) {
				return $got;
			}
		}
		return null;
	}
	public static function findContains($html,$selector,$contains,$out='first') {
		if ($html) {
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
	public static function curlLoad($url,$referer = false) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		if ($referer) {
			curl_setopt($curl, CURLOPT_REFERER, $referer);
		}
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.10) Gecko/2009042523 Ubuntu/12.10 (intrepid) Firefox/3.0.10");
		$headers = array();
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		$response = (curl_exec($curl));
		curl_close($curl);
		sleep(Settings::getOption('delay'));
		return $response;
	}
	public static function curlPostRequest($url,$referer,$params) {
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, 0);
		curl_setopt($curl, CURLOPT_COOKIEJAR, "cookie.txt");
		curl_setopt($curl, CURLOPT_COOKIEFILE, "cookie.txt");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_REFERER, $referer);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (X11; U; Linux x86_64; en-US; rv:1.9.0.10) Gecko/2009042523 Ubuntu/12.10 (intrepid) Firefox/3.0.10");
		$headers = array();
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
		$response = (curl_exec($curl));
		curl_close($curl);
		sleep(Settings::getOption('delay'));
		return $response;		
	}
	public static function loadHtmlFromString($str) {
		$html = str_get_html($str);
		if ($html) {
			return $html;
		} else {
			return null;
		}
	}
}