<?php
include('include/main.php');

if (empty($_GET['page'])) $_GET['page'] = "index";

if (!$_tpl->templateExists($_GET['page'] . ".tpl")) {
	echo "Seite nicht gefunden!";

} else {
	$_tpl->display($_GET['page'].".tpl");
}

?>