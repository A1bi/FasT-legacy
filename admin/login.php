<?php
include('../include/admin.php');

if ($_GET['action'] == "logout") {
	setcookie("admin", "", time()-1, "/");
	$msg = "Erfolgreich ausgelogged!";

} elseif (!empty($_POST['name'])) {
	$hash = md5($_POST['pass']);
	if ($_config['pass'] == $hash && $_POST['name'] == "Team") {
		setcookie("admin", $hash, time()+10800, "/");
		redirectTo("index.php");
	} else {
		$msg = "Falsche Zugangsdaten";
	}

}

$_tpl->assign("msg", $msg);
$_tpl->display("admin/login.tpl");
?>