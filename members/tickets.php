<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orders");
OrderManager::init();

function getOrdersFromInfo($orders) {
	$tmpOrders = array();
	foreach ($orders as $orderInfo) {
		$order = new Order();
		$order->init($orderInfo);
		$tmpOrders[] = $order;
	}
	
	return $tmpOrders;
}

if (!empty($_GET['order'])) {
	$order = OrderManager::getInstance()->getOrderById($_GET['order']);
	// not found ?
	if (!$order->getId()) {
		redirectTo("/mitglieder/tickets");
	}
	
	if ($_GET['action'] == "showDetails") {
		$_tpl->assign("order", $order);
		$_tpl->display("members_tickets_details.tpl");
			
	} else {
		loadComponent("queue");
		$queue = new Queue();
		
		switch ($_GET['action']) {
			case "markPaid":
				if ($order->markPaid()) {
					$payment = $order->getPayment();
					if ($payment['method'] == "transfer") {
						$queue->beginNewBatch();
						$queue->addJob("createPdf", $order->getId());
						$queue->addJob("mailTickets", $order->getId());
						$queue->exec("./include/queue");
					}
				}
				break;
				
			case "cancel":
				$order->cancel($_POST['reason']);
				break;
				
			case "approve":
				$order->approve();
				break;
		}
		
		redirectTo("/mitglieder/tickets" . (($_GET['goto'] != "overview") ? "?action=showDetails&order=".$order->getId() : ""));
	}
	

} elseif ($_GET['action'] == "charge") {
	loadComponent("dtaus");
	
	$result = $_db->query('SELECT id, sId, total, kName, kNo, blz, bank FROM orders WHERE status = 3');
	$charges = $result->fetchAll();
	
	if (count($charges)) {
		$chargeInfo = getData("charge");
	
		$_db->query('INSERT INTO orders_charges VALUES(null, ?)', array(time()));
		$chargeId = $_db->id();
		
		$dta = new DTAUS("LK", $chargeInfo['sender'], $chargeId);
		
		foreach ($charges as $charge) {
			$order = new Order();
			$order->init($charge);
			$payment = $order->getPayment();
			$paymentDetails = array("name" => $payment['name'], "account" => $payment['number'], "blz" => $payment['blz'], "bank" => $payment['bank']);
	
			$references = $chargeInfo['references'];
			$references[] = "ON".$order->getSId();
			$transaction = new Transaction("charge", $order->getTotal(), $paymentDetails, $references);
			$dta->addTransaction($transaction);
			
			$order->markPaid($chargeId);
		}
		
		//echo $dta->getData();
	}
	
	redirectTo("/mitglieder/tickets");


// nothing chosen -> show overview
} else {
	// statistics
	$result = $_db->query('SELECT date, type, COUNT(*) as number FROM orders_tickets WHERE cancelled = 0 GROUP BY date, type WITH ROLLUP');
	$ticketData = $result->fetchAll();
	
	$stats = array("total" => array());
	foreach ($ticketData as $ticket) {
		if ($ticket['date'] == "") {
			$arr = &$stats['total']['sum'];
		} else {
			$dateArr = &$stats['dates'][$ticket['date']];
			if ($ticket['type'] != "") {
				$arr = &$dateArr['types'][$ticket['type']];
				$stats['total']['types'][$ticket['type']] += $ticket['number'];
			} else {
				$arr = &$dateArr['sum'];
			}
		}
		
		$arr = $ticket['number'];
	}
	
	// calculate revenue
	foreach ($stats['dates'] as $key => $date) {
		$sum = 0;
		foreach (OrderManager::$theater['prices'] as $key2 => $price) {
			$sum += $price * $date['types'][(($key2 == "kids") ? 0 : 1)];
		}
		$stats['dates'][$key]['revenue'] = $sum;
		$stats['total']['revenue'] += $sum;
	}
	
	$_tpl->assign("stats", $stats);
	
	
	// orders to check
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(t.id) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status = 1
							AND			t.order = o.id
							GROUP BY	o.id
							ORDER BY	o.id DESC
							');
	$_tpl->assign("ordersCheck", getOrdersFromInfo($result->fetchAll()));
	
	
	// orders to pay
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(t.id) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status = 2
							AND			t.order = o.id
							GROUP BY	o.id
							ORDER BY	o.sId ASC
							');
	$_tpl->assign("ordersPay", getOrdersFromInfo($result->fetchAll()));
	
	
	// finished or closed orders
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(*) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status > 2
							AND			t.order = o.id
							GROUP BY	o.id
							ORDER BY	o.id DESC
							');
	$_tpl->assign("ordersFinished", getOrdersFromInfo($result->fetchAll()));
	
	// charges
	$result = $_db->query('SELECT COUNT(*) as number FROM orders WHERE status = 3');
	$charges = $result->fetch();
	$_tpl->assign("charges", $charges['number']);
	
	$result = $_db->query('	SELECT		COUNT(o.id) AS orders,
										SUM(o.total) AS total,
										c.date
							FROM		orders_charges AS c,
										orders AS o
							WHERE		o.charge = c.id
							GROUP BY	c.id
							');
	$_tpl->assign("oldCharges", $result->fetchAll());
	
	$_tpl->display("members_tickets.tpl");
}
?>