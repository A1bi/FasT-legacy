<?php

// enable error reporting
ini_set('display_errors','on');
error_reporting(E_ALL ^ E_NOTICE);

setlocale(LC_ALL, 'de_DE.UTF-8');

/**
 * redirects to given url
 *
 * @param string $url
 */
function redirectTo($url = "") {
	if (empty($url)) $url = $_SERVER['REQUEST_URI'];
	header("Location: " . $url);
	exit();
}

/**
 * includes the given component
 *
 * @param string $core
 */
function loadComponent($component) {
	$component = "./include/core/" . $component . ".php";
	// component exists?
	if (file_exists($component)) {
		require_once($component);
	}
}

function getData($name) {
	return json_decode(file_get_contents("./include/data/" . $name . ".json"), true);
}

/**
 * creates a random id with the given amount of digits
 *
 * @param int $digits
 * @return string
 */
function createId($digits, $table = "", $column = "", $numbers = false) {
	global $_db;
	$items = "abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXY";
	
	while (true) {
		if ($numbers) {
			$id = mt_rand(pow(10, $digits-1), pow(10, $digits)-1);
		
			
		} else {
			$id = "";
			for ($i = 0; $i < $digits; $i++) {
				$item = mt_rand(1, strlen($items));
				$id .= $items{$item};
			}
		}
		
		if (!empty($table)) {
			$row = $_db->query('SELECT '.$column.' FROM '.$table.' WHERE '.$column.' = ?', array($id))->fetch();
			if (empty($row[$column])) {
				break;
			}
		} else {
			break;
		}
	}
	
	return $id;
}

function limitAccess($groups) {
	global $_user;
	
	if (!in_array($_user['group'], $groups)) {
		redirectTo("/mitglieder/login");
	}
}


// check if SSL required
$sslRequired = array("order");

if ($_vars['sslRequired'] || in_array(substr($_SERVER['PHP_SELF'], 1, -4), $sslRequired)) {
	if (!$_SERVER['HTTPS']) {
		$redirect = 1;
	}
} else if ($_SERVER['HTTPS']) {
	$redirect = 2;
}
if (!empty($redirect)) {
	redirectTo("http" . (($redirect == 1) ? "s" : "") . "://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
}


// get config
require("./include/config.inc.php");

// components to load
$comps = array("templates", "database");
foreach ($comps as $comp) {
	loadComponent($comp);
}


// user management
$_user = array("group" => 0);

// start session
// only through HTTPS
if ($_SERVER['HTTPS']) {

	ini_set("session.use_only_cookies", 1);
	session_name("FasT_sess");
	session_set_cookie_params(1800, "/", $_SERVER['SERVER_NAME'], true);
	session_start();
	
	$_db = new database();
	
	// check if logged in
	if (is_array($_SESSION['user'])) {
		// look in database for given user
		$result = $_db->query('SELECT id, name, `group`, realname, email FROM users WHERE id = ? AND pass = ?', array($_SESSION['user']['id'], $_SESSION['user']['pass']));
		$user = $result->fetch();
	
		// found ?
		if (!empty($user['id'])) {
			$keys = array("id", "name", "group", "realname", "email");
			foreach ($keys as $key) {
				$_user[$key] = $user[$key];
			}
			
		} else {
			// not correct -> delete session
			unset($_SESSION['user']);
		}
	}
	
	$_tpl->assign("_user", $_user);
}
	
?>