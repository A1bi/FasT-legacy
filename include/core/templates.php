<?php
/**
 * initiates smarty template engine
 */

require('/usr/share/php/smarty/Smarty.class.php');

// init object
global $_tpl, $_config;
$_tpl = new Smarty();

$config = $_config;
// for security: remove access to database config
unset($config['db']);
$_tpl->assign("_config", $config);

// configure
$_tpl->setTemplateDir('./include/templates/source');
$_tpl->setCompileDir('./include/templates/compiled');
$_tpl->setCacheDir('./include/templates/cache');
$_tpl->setConfigDir('./include/templates/configs');

// plugins
function smarty_modifier_append_version($file, $append = "ver") {
	return $file . "?" . $append . "=" . @filemtime("." . $file);
}

function smarty_modifier_date_format_x($string, $format = "%b %e, %Y", $dateFormat = "%d.%m.%y", $default_date = "") {
	global $_tpl;
	$_tpl->loadPlugin('smarty_modifier_date_format');

	if (strpos($format, "%@") !== false) {
		$oldDate = date("Ymd", $string);
		if ($oldDate == date("Ymd")) {
			$date = "Heute";
		} elseif ($oldDate == date("Ymd", time()-86400)) {
			$date = "Gestern";
		} else {
			$date = strftime($dateFormat, $string);
		}
		
		$format = str_replace("%@", $date, $format);
	}
	
	return smarty_modifier_date_format($string, $format, $default_date);
}

function smarty_modifier_time_difference($string, $unit = "days") {
	$divisors = array("hours" => 3600, "days" => 86400);

	return round((time() - $string) / $divisors[$unit]);
}

?>
