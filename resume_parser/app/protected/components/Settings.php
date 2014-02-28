<?
class Settings extends CComponent
{
    public static function getOption($option_name) {
		$settings=array('parse_days' => 2,'delay' => 1);
		return $settings[$option_name];
    }
}