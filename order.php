<?php
include('./include/main.php');

requireSSL();

if ($_GET['ajax']) {
	$response = array();
	
	switch ($_GET['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => array("kids" => 6, "adults" => 12)
			);
			
			$dates = array(1 => "1345222800", 2 => "1345309200", 3 => "1345381200", 4 => "1345827600", 5 => "1346432400", 6 => "1346518800");
			setlocale(LC_ALL, 'de_DE');
			foreach ($dates as $key => $date) {
				$response['info']['dates'][$key] = strftime("%A, den %d. %B um %H Uhr", $date);
			}

			if (!is_array($_SESSION['order']) || $_SESSION['order']['lastUpdate']+600 < time()) {
				$_SESSION['order'] = array(
					"step" => 0,
					"lastUpdate" => time(),
					"date" => 0,
					"number" => array("kids" => 0, "adults" => 0),
					"address" => array("firstname" => "", "lastname" => "", "fon" => "", "email" => ""),
					"payment" => array("method" => "", "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => false),
					"accpeted" => false
				);
			}
			$response['order'] = $_SESSION['order'];
			
			break;
	}
	
	echo json_encode($response);
	
} else {
	$_tpl->display("order.tpl");
}

?>
