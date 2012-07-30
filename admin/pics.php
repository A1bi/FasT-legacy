<?php
include('../include/admin.php');
loadComponent("resize");
$_db = new database;
$resize = new resize;

set_time_limit(0);

if (!empty($_GET['id']) && (!empty($_POST['submit']) || $_GET['import'])) {

	$importDir = $_base."gfx/cache/import/";
	
	if (!$_GET['import']) {
		$filename = $importDir."uploaded";
		if (move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
			chmod($filename, 0777);
			$ok = true;

			$msg = "Das Bild wurde erfolgreich hochgeladen und hinzugefügt!";
		} else {
			$msg = "Fehler beim Hochladen!";
		}
		
	}
	
	$i = 0;
	if ($handle = opendir($importDir)) {
		while (false !== ($file = readdir($handle))) {
			if (substr($file, 0, 1) == ".") continue;
			
			$id = createId(6);
			$filename = $_base."gfx/cache/gallery/".$_GET['id']."/full/".$id;
			rename($importDir.$file, $filename);
			
			$pics = $_db->query('SELECT COUNT(*) AS count FROM gallery_pics WHERE gallery = ?', array($_GET['id']))->fetch();

			$_db->query('INSERT INTO gallery_pics VALUES (?, ?, ?, ?)', array($id, $_GET['id'], (!empty($_POST['desc'])) ? $_POST['desc'] : "", $pics['count']));
			$resize->gallery($id, $_GET['id']);
			$i++;
		}

		closedir($handle);
		$msg = $i." Fotos erfolgreich importiert!";
	}
	
	$_tpl->assign("msg", $msg);

} else {
	$result = $_db->query('SELECT * FROM gallery');
	$_tpl->assign("galleries", $result->fetchAll());
}

$_tpl->display("admin/pics.tpl");
?>
