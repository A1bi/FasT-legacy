<?php
include('./include/main.php');

$_db = new database;
loadComponent("orderManager");
OrderManager::init();
loadComponent("ticketStats");

$dates = OrderManager::getDates();
if (array_pop($dates) + 86400 < time()) {
	$_tpl->display("order_none.tpl");exit;
}

if ($_GET['ajax']) {
	$response = array();
	$stats = new TicketStats;
	
	switch ($_POST['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => OrderManager::getTicketTypes(OrderType::Online)
			);
			
			foreach (OrderManager::getDates() as $key => $date) {
				$stat = $stats->getValue($key, -1, -1);
				$response['info']['dates'][$key] = array(
					"string" => OrderManager::getStringForDate($date),
					"ticketsLeft" => 300 - $stat['number'],
					"transferEnabled" => $date - 259200 > time(),
					"expired" => $date < time() - 3600
				);
			}

			if (!is_array($_SESSION['order']) || $_SESSION['order']['lastUpdate']+600 < time()) {
				$_SESSION['order'] = array(
					"step" => 0,
					"lastUpdate" => time(),
					"date" => 0,
					"number" => (object)array(),
					"address" => array("gender" => 1, "firstname" => "", "lastname" => "", "affiliation" => "", "plz" => 0, "fon" => "", "email" => ""),
					"payment" => array("method" => "", "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => false),
					"accepted" => false,
					"total" => 0
				);
			}
			$response['order'] = $_SESSION['order'];
			
			break;
			
		case "placeOrder":
			$payMethods = array("charge" => OrderPayMethod::Charge, "transfer" => OrderPayMethod::Transfer);
			$_POST['order']['payment']['method'] = $payMethods[$_POST['order']['payment']['method']];
			
			$order = new Order();
			$order->create(OrderType::Online);
			
			if (!$order->checkAndSetAddress($_POST['order']['address'])) {
				$error = "address";
				
			} elseif (!$order->checkAndSetPayment($_POST['order']['payment']) || ($_POST['order']['payment']['method'] == OrderPayMethod::Charge && $_POST['order']['payment']['accepted'] != "true")) {
				$error = "payment";
				
			} elseif ($_POST['order']['accepted'] != "true") {
				$error = "accepted";
				
			} else {
				foreach (OrderManager::getTicketTypes(OrderType::Online) as $type => $price) {
					for ($i = 0; $i < $_POST['order']['number'][$type]; $i++) {
						if (!$order->addTicket($type, $_POST['order']['date'])) {
							$error = "ticket";
							break;
						}
					}
				}
			
				if (empty($error) && ($order->getTotal() != $_POST['order']['total'] || $order->getTotal() == 0)) $error = "total";
			}
			
			if (empty($error)) {
				$order->save();
			
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
				$response['status'] = "error " . $error;
			}
			
			break;
	}
	
	echo json_encode($response);
	
} else {
	$_tpl->display("order.tpl");
}

?>
