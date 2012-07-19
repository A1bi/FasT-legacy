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
$_tpl->setTemplateDir('./include/templates');
$_tpl->setCompileDir('./include/templates/compiled');
$_tpl->setCacheDir('./include/templates/cache');
$_tpl->setConfigDir('./include/templates/configs');

// register custom functions
function getFileTimestamp($params) {
	return "?ver=" . @filemtime("." . $params['file']);
}

$_tpl->registerPlugin("function", "fileVersion", "getFileTimestamp");

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

?>
