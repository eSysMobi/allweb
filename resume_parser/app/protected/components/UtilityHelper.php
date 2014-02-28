<?
class UtilityHelper extends CComponent
{
    public static function loadHelper($helper_name)
    {
        require_once( dirname(__FILE__) . '/../helpers/'.$helper_name.'.php');
    }
	public static function days_ago($str_date) {
		$now = time()+ 60*60;
		$your_date = strtotime($str_date);
		$datediff = $now - $your_date;
		return($datediff/(60*60*24));
	}
	public static function formatPhone($phone) {
		return str_replace(array(' ',')','-','(','+','.'),'',$phone);
	}
	function format_email ($string) {
		foreach(preg_split('/\s/', $string) as $token) {
			$email = filter_var(filter_var($token, FILTER_SANITIZE_EMAIL), FILTER_VALIDATE_EMAIL);
			if ($email !== false) {
				return $email;
				// $emails[] = $email;
			}
		}
		// return $emails[0];
	}
}