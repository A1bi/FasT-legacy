<?php
include('../include/admin.php');
loadComponent("resize");
$_db = new database;

if (!empty($_POST['submit'])) {

	$resize = new resize;
	$id = createId(6);
	$filename = $_base."gfx/cache/slides/full/".$id;

	if (move_uploaded_file($_FILES['file']['tmp_name'], $filename)) {
		chmod($filename, 0777);
		$_db->query('INSERT INTO slides VALUES (?)', array($id));
		$resize->slide($id);
		$msg = "Das Bild wurde erfolgreich hochgeladen und hinzugefügt!";
		$result = $_db->query('SELECT * FROM slides');
		$slides = $result->fetchAll();
		$js = $_base."gfx/cache/slides.js";
		$f = fopen($js, "w+");
		fwrite($f, "var slides = [");
		foreach ($slides as $i => $slide) {
			fwrite($f, '"'.$slide['id'].'"');
			if ($i < count($slides)-1) {
				fwrite($f, ',');
			}
		}
		fwrite($f, '];');
		fclose($f);
		chmod($js, 0777);
	} else {
		$msg = "Fehler beim Hochladen!";
	}

	$_tpl->assign("msg", $msg);

}

$_tpl->display("admin_slides.tpl");
?>
