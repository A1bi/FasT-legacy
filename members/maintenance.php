<?php
include('../include/members.php');

limitAccess(array(2));

switch ($_GET['action']) {
	
	case "gallerySize":
		loadComponent("resize");
		$resize = new resize;
		
		$result = $_db->query('SELECT id, gallery FROM gallery_pics');
		while ($pic = $result->fetch()) {
			$resize->gallery($pic['id'], $pic['gallery']);
		}
		break;
}

?>