<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orders");
OrderManager::init();

if (!empty($_GET['order'])) {
	
	
// nothing chosen -> show overview
} else {
	// statistics
	$result = $_db->query('SELECT date, type, COUNT(*) as number FROM orders_tickets WHERE cancelled = 0 GROUP BY date, type WITH ROLLUP');
	$ticketData = $result->fetchAll();
	
	$stats = array();
	foreach ($ticketData as $ticket) {
		if ($ticket['date'] == "") {
			$arr = &$stats['sum'];
		} else {
			$dateArr = &$stats['dates'][$ticket['date']];
			if ($ticket['type'] != "") {
				$arr = &$dateArr['types'][$ticket['type']];
			} else {
				$arr = &$dateArr['sum'];
			}
		}
		
		$arr = $ticket['number'];
	}
	
	// calculate revenue
	$total = 0;
	foreach ($stats['dates'] as $key => $date) {
		$sum = 0;
		foreach (OrderManager::$theater['prices'] as $key2 => $price) {
			$sum += $price * $date['types'][(($key2 == "kids") ? 0 : 1)];
		}
		$stats['dates'][$key]['revenue'] = $sum;
		$total += $sum;
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
							WHERE		o.payMethod = "charge"
							AND			o.status < 1
							AND			t.order = o.id
							GROUP BY	o.id
							');
	$_tpl->assign("ordersCheck", $result->fetchAll());
	
	
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
							WHERE		o.payMethod = "transfer"
							AND			o.paid = 0
							AND			o.status < 2
							AND			t.order = o.id
							GROUP BY	o.id
							');
	$_tpl->assign("ordersPay", $result->fetchAll());
	
	
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
							WHERE		o.status = 2
							AND			t.order = o.id
							GROUP BY	o.id
							');
	$_tpl->assign("ordersFinished", $result->fetchAll());
	
	$_tpl->display("members_tickets.tpl");
}
?>