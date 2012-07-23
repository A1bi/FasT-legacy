<?php
include('../include/members.php');

if ($_POST['submit']) {
	if (!validatePassword($_POST['old'], $_user['pass'])) {
		$msg = "Altes Passwort stimmt nicht!";
		
	} elseif ($_POST['new1'] != $_POST['new2']) {
		$msg = "Die neuen Passwörter stimmen nicht überein!";
		
	} elseif (strlen($_POST['new1']) < 6) {
		$msg = "Das neue Passwort muss aus mindestens 6 Zeichen bestehen!";
	
	} else {
		$hash = getHashFromPassword($_POST['new1']);
		$_db->query('UPDATE users SET pass = ?, passChanged = NOW() WHERE id = ?', array($hash, $_user['id']));
		$_SESSION['user']['pass'] = $hash;
		
		if ($_COOKIE['FasT_userpass'] == $_user['pass']) {
			setcookie("FasT_userpass", $hash, time() + 604800, "/", $_SERVER['SERVER_NAME'], true);
		}
		
		$msg = "Das Passwort wurde erfolgreich geändert!";
	}
	
	$_tpl->assign("msg", $msg);
}

$_tpl->display("members_settings.tpl");
?>