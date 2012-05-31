<?php
include('./include/main.php');

requireSSL();
setlocale(LC_ALL, 'de_DE');

$_db = new database;
loadComponent("orders");

function getStringForDate($date) {
	return strftime("%A, den %d. %B um %H Uhr", $date);
}

if ($_GET['ajax']) {
	$response = array();
	
	switch ($_POST['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => OrderManager::$prices
			);
			
			foreach (OrderManager::$dates as $key => $date) {
				$response['info']['dates'][$key] = OrderManager::getStringForDate($date);
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
			$order = new Order($_POST['order']);
			
			if ($order != null) {
				
				// mail info to customer
				$order->mailConfirmation();
				
				$payment = $order->getPayment();
				if ($payment['method'] == "charge") {
					// create pdf containing tickets and send it
					$order->createPdf();
					$order->mailTickets();
				}
				
				$response['status'] = "ok";
			
			} else {
				$response['status'] = "error";
			}
			
			break;
	}
	
	echo json_encode($response);
	
} else {
	$_tpl->display("order.tpl");
}

?>
