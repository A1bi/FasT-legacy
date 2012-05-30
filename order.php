<?php
include('./include/main.php');

requireSSL();
$_db = new database;

if ($_GET['ajax']) {
	$response = array();
	
	$dates = array(1 => "1345222800", 2 => "1345309200", 3 => "1345381200", 4 => "1345827600", 5 => "1346432400", 6 => "1346518800");
	$prices = array("kids" => 6, "adults" => 12);
	
	switch ($_POST['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => $prices
			);
			
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
					"accepted" => false
				);
			}
			$response['order'] = $_SESSION['order'];
			
			break;
			
		case "placeOrder":
			$order = $_POST['order'];
			$ok = true;
			$error = "";
			
			if (is_array($_SESSION['order'])) {
				
				// check date
				if (empty($dates[$order['date']])) {
					$ok = false;
					$error = "date";
				}
				
				// check numbers and total
				$total = 0;
				foreach ($prices as $type => $price) {
					$total += $order['number'][$type]*$price;
				}
				if ($total == 0 || $total != $order['total']) {
					$ok = false;
					$error = "total";
				}
				
				// check address
				foreach ($_SESSION['order']['address'] as $key => $value) {
					if (empty($order['address'][$key]) || ($key == "email" && !preg_match("#^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z]){2,9}$#", $order['address'][$key]))) {
						$ok = false;
						$error = "address";
					}
				}
				
				// check payment
				if ($order['payment']['method'] != "transfer") {
					foreach ($_SESSION['order']['payment'] as $key => $value) {
						if (empty($order['payment'][$key])) {
							$ok = false;
							$error = "payment";
						}
					}
				}
				
				// check if accepted TOS
				if (!$order['accepted']) {
					$ok = false;
					$error = "not accepted";
				}
				
			} else {
				$ok = false;
				$error = "unknown";
			}
			
			if ($ok) {
				$sId = createId(6, "orders", "sId", true);
				
				// create order in db
				$_db->query('INSERT INTO orders VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, "")',
						array($sId, $order['address']['firstname'], $order['address']['lastname'], $order['address']['fon'], $order['address']['email'], $order['payment']['method'], $order['payment']['name'], $order['payment']['number'], $order['payment']['blz'], $order['payment']['bank'], ($order['payment']['accepted'] == "true") ? 1 : 0, ($order['accepted'] == "true") ? 1 : 0, $order['total'], time(), $_SERVER['REMOTE_ADDR']));
				$orderId = $_db->id();
				
				// create ticket in db
				$t = 0;
				foreach ($_SESSION['order']['number'] as $type => $val) {
					for ($i = 0; $i < $order['number'][$type]; $i++) {
						$sId = createId(6, "orders_tickets", "sId", true);
						$_db->query('INSERT INTO orders_tickets VALUES (null, ?, ?, ?, ?, 0, 0, "", 0)', array($sId, $orderId, $order['date'], $t));
					}
					$t++;
				}
			}
			
			$response['status'] = ($ok) ? "ok" : "error";
			$response['error'] = $error;
			
			break;
	}
	
	echo json_encode($response);
	
} else {
	$_tpl->display("order.tpl");
}

?>
