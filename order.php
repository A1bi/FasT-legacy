<?php
include('./include/main.php');

$_db = new database;
loadComponent("orders");

if ($_GET['ajax']) {
	$response = array();
	OrderManager::init();
	
	switch ($_POST['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => OrderManager::$theater['prices']
			);
			
			foreach (OrderManager::$theater['dates'] as $key => $date) {
				$response['info']['dates'][$key] = OrderManager::getStringForDate($date);
			}

			if (!is_array($_SESSION['order']) || $_SESSION['order']['lastUpdate']+600 < time()) {
				$_SESSION['order'] = array(
					"step" => 0,
					"lastUpdate" => time(),
					"date" => 0,
					"number" => array(),
					"address" => array("gender" => 1, "firstname" => "", "lastname" => "", "plz" => 0, "fon" => "", "email" => ""),
					"payment" => array("method" => "", "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => false),
					"accepted" => false,
					"total" => 0
				);
			}
			$response['order'] = $_SESSION['order'];
			
			break;
			
		case "placeOrder":
			$order = new Order();
			
			$payMethods = array("charge" => OrderPayMethod::Charge, "transfer" => OrderPayMethod::Transfer);
			$_POST['order']['payment']['method'] = $payMethods[$_POST['order']['payment']['method']];
			
			if ($order->create($_POST['order'])) {
			
				loadComponent("queue");
				$queue = new Queue();
				$queue->beginNewBatch();
				
				// mail info to customer
				$queue->addJob("mailConfirmation", $order->getId());
				
				$payment = $order->getPayment();
				if ($payment['method'] == OrderPayMethod::Charge) {
					// create pdf containing tickets and send it
					$queue->addJob("createPdf", $order->getId());
					$queue->addJob("mailTickets", $order->getId());
				}
				
				$queue->exec("./include/queue");
				
				$response['order'] = array("sId" => $order->getSId());
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
