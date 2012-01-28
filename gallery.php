<?php
include('./include/main.php');
$_db = new database;

if (!empty($_GET['id'])) {
	if (!$_GET['pic']) $_GET['pic'] = 1;

	$result = $_db->query('SELECT title, copyright FROM gallery WHERE id = ?', array($_GET['id']));
	$gallery = $result->fetch();

	$result = $_db->query('SELECT COUNT(*) as count FROM gallery_pics WHERE gallery = ?', array($_GET['id']));
	$pics = $result->fetch();
	$pics = $pics['count'];

	$result = $_db->query('SELECT * FROM gallery_pics WHERE gallery = ? ORDER BY pos ASC LIMIT '.(intval($_GET['pic'])-1).', 1', array($_GET['id']));
	$pic = $result->fetch();

	$s = 1;
	while ($s <= $pics) {
		if ($s == $_GET['pic']) {
			$navi .= $s;
		} elseif ($s > $_GET['pic']+2 && $s < $pics) {
			$navi .= " ... ";
			$s = $pics-1;
		} elseif ($s > 1 && $s < $_GET['pic']-2) {
			$navi .= " ... ";
			$s = $_GET['pic']-3;
		} else {
			$navi .= '<a href="/gallery/'.$pic['gallery'].'/'.$s.'#pic">'.$s.'</a>';
		}
		if ($s != $pics) {
			$navi .= ", ";
		}
		$s++;
	}

	$_tpl->assign(array("pic" => $pic, "gallery" => $gallery, "pics" => $pics, "navi" => $navi));
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
