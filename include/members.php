<?php
chdir("../");

$_vars['sslRequired'] = true;
include('./include/main.php');

// if the user is not a member -> redirect to login page
if ($_SERVER['PHP_SELF'] != "/members/login.php") {
	// member (1) or board member (2) ?
	limitAccess(array(1, 2));
}
?>
