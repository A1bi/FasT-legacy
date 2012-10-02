<?php
include('./include/main.php');
$_db = new database;

if (!empty($_GET['id'])) {
	if (!$_GET['pic']) $_GET['pic'] = 1;

	$result = $_db->query('SELECT id, title, copyright FROM gallery WHERE id = ?', array($_GET['id']));
	$gallery = $result->fetch();

	$result = $_db->query('SELECT id, text FROM gallery_pics WHERE gallery = ? ORDER BY pos ASC', array($_GET['id']));
	$pics = $result->fetchAll();

	$_tpl->assign(array("pics" => $pics, "gallery" => $gallery));
	$_tpl->display("gallery_show.tpl");

} else {

	$galleries = array();
	$result = $_db->query('SELECT * FROM gallery ORDER BY pos ASC');
	while ($gallery = $result->fetch()) {

		$result2 = $_db->query('SELECT id FROM gallery_pics WHERE gallery = ? ORDER BY pos ASC LIMIT 0,4', array($gallery['id']));
		$pics = $result2->fetchAll();
		$galleries[] = array_merge($gallery, array("pics" => $pics));

	}

	$_tpl->assign("galleries", $galleries);
	$_tpl->display("gallery.tpl");
}

?>
