<?php
include('../include/members.php');

function redirectToMembers() {
	global $_user;
	
	redirectTo("/mitglieder" . (($_user['group'] == 2) ? "/tickets" : ""));
}

if ($_user['id']) {
	if ($_GET['action'] == "logout") {
		unset($_SESSION['user']);
		
		redirectTo("/");
	} else {
		redirectToMembers();
	}
	
} else if ($_POST['login'] && !empty($_POST['name']) && !empty($_POST['pass'])) {
	$result = $_db->query('SELECT id, `group`, pass FROM users WHERE name = ?', array($_POST['name']));
	$user = $result->fetch();
	
	if ($user['id'] && validatePassword($_POST['pass'], $user['pass'])) {
		$_db->query('UPDATE users SET lastLogin = ? WHERE id = ?', array(time(), $user['id']));
		$_SESSION['user'] = array("id" => $user['id'], "pass" => $user['pass']);
		$_user = $user;
		
		redirectToMembers();
	}
	
	// just for safety
	unset($user);
	
	$_tpl->assign("msg", "Zugangsdaten stimmen nicht!");

}

$_tpl->display("members_login.tpl");
?>