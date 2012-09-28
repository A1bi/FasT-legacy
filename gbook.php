<?php
include('include/main.php');

$codes = array("MBSD", "KMPY", "LRWK", "T4A1", "S74P", "ZN6X", "FGRN", "KD5W", "ZUS5", "H73K");
$_db = new database;

/*
 * neuen Eintrag verfassen
 */
if ($_GET['action'] == "new") {

	if (!empty($_POST['text'])) {
		if (empty($_POST['code']) || $_POST['code'] != $codes[$_POST['codenr']]) {
			$_tpl->assign("msg", "Der eingegebene Code stimmt nicht mit der Grafik überein!");
		} else {
			$_db->query('INSERT INTO gbook (name, text) VALUES (?, ?)', array($_POST['name'], $_POST['text']));
			mail("albi@albisigns.de", "Neuer Eintrag FasT", "guck halt nach!");
			redirectTo("/gästebuch");
		}
	}

	$_tpl->assign("code", rand(1, count($codes))-1);
	$_tpl->display("gbook_new.tpl");

/*
 * Einträge anzeigen
 */
} else {
	if (empty($_GET['page'])) {
		$_GET['page'] = 1;
	}
	$limit = 8;

	$result = $_db->query('SELECT id FROM gbook');
	$rows = $_db->rows($result);
	$s = 1;
	$pages = ceil($rows/$limit);
	while ($s <= $pages) {
		if ($s == $_GET['page']) {
			$navi .= $s;
		} else {
			$navi .= "<a href=\"/gästebuch/$s\">$s</a>";
		}
		if ($s != $pages) {
			$navi .= ", ";
		}
		$s++;
	}
	$_tpl->assign("navi", $navi);

	$start = ($_GET['page']-1) * $limit;
	$result = $_db->query('SELECT *, UNIX_TIMESTAMP(time) AS time FROM gbook ORDER BY id DESC LIMIT '.$start.', '.$limit);
	$_tpl->assign("entries", $_db->fetchAll($result));
	$_tpl->display("gbook.tpl");
}
?>