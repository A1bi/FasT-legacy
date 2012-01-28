<?php
include('../include/admin.php');
loadComponent("resize");
$_db = new database;

if (!empty($_GET['id']) && !empty($_POST['submit'])) {

	$resize = new resize;
	$id = createId(6);
	$filename = $_base."gfx/cache/gallery/".$_GET['id']."/full/".$id;

	if (move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
		chmod($filename, 0777);
		
		$pics = $_db->query('SELECT COUNT(*) AS count FROM gallery_pics WHERE gallery = ?', array($_GET['id']))->fetch();
		
		$_db->query('INSERT INTO gallery_pics VALUES (?, ?, ?, ?)', array($id, $_GET['id'], $_POST['desc'], $pics['count']));
		$resize->gallery($id, $_GET['id']);
		$msg = "Das Bild wurde erfolgreich hochgeladen und hinzugefügt!";
	} else {
		$msg = "Fehler beim Hochladen!";
	}
	
	$_tpl->assign("msg", $msg);

} else {
	$result = $_db->query('SELECT * FROM gallery');
	$_tpl->assign("galleries", $result->fetchAll());
}

$_tpl->display("admin_pics.tpl");
?>
