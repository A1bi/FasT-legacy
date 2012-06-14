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
	$hash = md5($_POST['pass']);
	$result = $_db->query('SELECT id, `group` FROM users WHERE name = ? AND pass = ?', array($_POST['name'], $hash));
	$user = $result->fetch();
	
	if ($user['id']) {
		$_db->query('UPDATE users SET lastLogin = ? WHERE id = ?', array(time(), $user['id']));
		$_SESSION['user'] = array("id" => $user['id'], "pass" => $hash);
		$_user = $user;
		
		redirectToMembers();
	}
	
	$_tpl->assign("msg", "Zugangsdaten stimmen nicht!");

}

$_tpl->display("members_login.tpl");
?>