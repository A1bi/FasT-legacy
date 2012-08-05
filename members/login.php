<?php
include('../include/members.php');

function redirectToMembers() {
	global $_user;
	
	redirectTo("/mitglieder" . (($_user['group'] == 2) ? "/bestellungen" : ""));
}

if ($_user['id']) {
	if ($_GET['action'] == "logout") {
		unset($_SESSION['user']);
		setcookie("FasT_userid", "", 0, "/", $_SERVER['SERVER_NAME'], true);
		setcookie("FasT_userpass", "", 0, "/", $_SERVER['SERVER_NAME'], true);
		
		redirectTo("/");
	} else {
		redirectToMembers();
	}
	
} else if ($_POST['login'] && !empty($_POST['name']) && !empty($_POST['pass'])) {
	$result = $_db->query('SELECT id, `group`, pass FROM users WHERE name = ?', array($_POST['name']));
	$user = $result->fetch();
	
	if ($user['id'] && validatePassword($_POST['pass'], $user['pass'])) {
		$_db->query('UPDATE users SET lastLogin = NOW() WHERE id = ?', array($user['id']));
		$_SESSION['user'] = array("id" => $user['id'], "pass" => $user['pass']);
		
		if (!empty($_POST['stay'])) {
			setcookie("FasT_userid", $user['id'], time() + 604800, "/", $_SERVER['SERVER_NAME'], true);
			setcookie("FasT_userpass", $user['pass'], time() + 604800, "/", $_SERVER['SERVER_NAME'], true);
		}
		
		$_user = $user;
		
		redirectToMembers();
	}
	
	// just for safety
	unset($user);
	
	$_tpl->assign("msg", "Zugangsdaten stimmen nicht!");

}

$_tpl->display("members/login.tpl");
?>