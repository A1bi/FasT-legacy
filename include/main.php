<?php

// enable error reporting
ini_set('display_errors','on');
error_reporting(E_ALL ^ E_NOTICE);

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
	global $_base;

	$component = "./include/core/" . $component . ".php";
	// component exists?
	if (file_exists($component)) {
		require_once($component);
	}
}

/**
 * creates a random id with the given amount of digits
 *
 * @param int $digits
 * @return string
 */
function createId($digits) {
	$items = "abcdefghijkmnpqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXY";
	for ($i = 0; $i < $digits; $i++) {
		$item = mt_rand(1, strlen($items));
		$id .= $items{$item};
	}
	return $id;
}

function requireSSL() {
	if (!$_SERVER['HTTPS']) {
		redirectTo("https://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	}
}

function rejectSSL() {
	if ($_SERVER['HTTPS']) {
		redirectTo("http://" . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']);
	}
}

function limitAccess($groups) {
	global $_user;
	
	foreach ($_user['groups'] as $group) {
		if (in_array($group, $groups)) {
			return;
		}
	}
	
	redirectTo("/");
}

// get config
require("./include/config.inc.php");
$_base = $_SERVER['DOCUMENT_ROOT'] . "/";

// components to load
$comps = array("templates", "database");
foreach ($comps as $comp) {
	loadComponent($comp);
}

// start session
ini_set("session.use_only_cookies", 1);
session_name("FasT_sess");
session_set_cookie_params(0, "/");
session_start();

$_user = array("groups" => array());

// check if logged in
if (is_array($_SESSION['user'])) {
	// look in database for given user
	//$result = $_db->query('SELECT id, name, credit, email FROM users WHERE id = ? AND pass = ?', array($_SESSION['user']['id'], $_SESSION['user']['pass']));

	// found ?
	if (!empty($user['id'])) {
		array_merge($_user, $user);
		// check groups
		
	} else {
		// not correct -> delete session
		unset($_SESSION['user']);
	}
}

$_tpl->assign("_user", $_user);
	
?>