<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orders");
OrderManager::init();

function getOrdersFromInfo($orders) {
	$tmpOrders = array();
	foreach ($orders as $order) {
		$tmpOrders[] = new Order($order, false);
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
		switch ($_GET['action']) {
			case "markPaid":
				$order->markPaid();
				break;
				
			case "cancel":
				$order->cancel($_POST['reason']);
				break;
				
			case "approve":
				$order->approve();
				break;
		}
		
		redirectTo("/mitglieder/tickets?action=showDetails&order=".$order->getId());
	}
	
	
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
							ORDER BY	o.id DESC
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
	
	$_tpl->display("members_tickets.tpl");
}
?>