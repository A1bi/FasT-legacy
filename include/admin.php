<?php
chdir("../");
include('include/main.php');

if (!$_vars['admin'] && $_SERVER['PHP_SELF'] != "/admin/login.php") {
	redirectTo("login.php");
}
?>
