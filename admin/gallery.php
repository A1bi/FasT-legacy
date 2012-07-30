<?php
include('../include/admin.php');

if (!empty($_POST['title'])) {
	$id = createId(6);
	$_db = new database;
	$_db->query('INSERT INTO gallery VALUES (?, ?, ?, 0)', array($id, $_POST['title'], $_POST['copyright']));
	mkdir($_base."gfx/cache/gallery/".$id, 0777);
	mkdir($_base."gfx/cache/gallery/".$id."/small/", 0777);
	mkdir($_base."gfx/cache/gallery/".$id."/medium/", 0777);
	mkdir($_base."gfx/cache/gallery/".$id."/full/", 0777);

	$_tpl->assign(array("id" => $id, "success" => true));
}

$_tpl->display("admin/gallery.tpl");
?>
