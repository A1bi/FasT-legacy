<?php
include('../include/members.php');

if ($_user['id']) {
	redirectTo("/mitglieder");
	
} else if ($_POST['login'] && !empty($_POST['name']) && !empty($_POST['pass'])) {
	$hash = md5($_POST['pass']);
	$result = $_db->query('SELECT id FROM users WHERE name = ? AND pass = ?', array($_POST['name'], $hash));
	$user = $result->fetch();
	
	if ($user['id']) {
		$_db->query('UPDATE users SET lastLogin = ? WHERE id = ?', array(time(), $user['id']));
		$_SESSION['user'] = array("id" => $user['id'], "pass" => $hash);
		
		redirectTo("/mitglieder");
	}
	
	$_tpl->assign("msg", "Zugangsdaten stimmen nicht!");

}

$_tpl->display("members_login.tpl");
?>