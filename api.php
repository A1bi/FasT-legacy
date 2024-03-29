<?php
include('./include/main.php');

$_db = new database;
loadComponent("orderManager");

$request = json_decode(file_get_contents("php://input"));
$response = array();

switch ($request->action) {
	case "getOrders":
		if (!$request->date) $request->date = 1;
		
		$result = $_db->query('SELECT o.id FROM orders AS o, orders_tickets AS t WHERE t.date = ? AND o.id = t.order GROUP BY o.id', array($request->date));
		$orders = array();
		while ($row = $result->fetch()) {
			$order = OrderManager::getOrderById($row['id']);
			
			$tickets = array();
			foreach ($order->getTickets() as $ticket) {
				$tickets[] = array(
					"id" => $ticket->getId(),
					"sId" => $ticket->getSId(),
					"type" => $ticket->getType(),
					"voided" => $ticket->getVoided()
				);
			}
			
			$address = $order->getAddress();
			$payment = $order->getPayment();
			
			$orders[] = array(
				"id" => $order->getId(),
				"sId" => $order->getSId(),
				"type" => $order->getType(),
				"total" => $order->getTotal(),
				"address" => array(
					"firstname" => $address['firstname'],
					"lastname" => $address['lastname'],
					"affiliation" => $address['affiliation']
				),
				"payMethod" => $payment['method'],
				"paid" => (bool)$order->isPaid(),
				"cancelled" => array(
					"cancelled" => (bool)$order->isCancelled(),
					"cancelReason" => $order->getCancelReason()
				),
				"notes" => $order->getNotes(),
				"tickets" => $tickets
			);
		}
		
		$response['status'] = "ok";
		$response['orders'] = $orders;
		break;
		
	case "voidTickets":
		foreach ($request->tickets as $ticket) {
			$_db->query('UPDATE orders_tickets SET voided = ? WHERE id = ?', array($ticket->voided, $ticket->id));
		}
		break;
}

echo json_encode($response);

?>