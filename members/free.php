<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orderManager");
OrderManager::init();

if ($_GET['action'] == "new") {
	if ($_POST['confirm']) {
	
		if ($_POST['number'] < 1) {
			$_tpl->assign("error", "Bitte wählen Sie mindestens eine Karte aus!");
			
		} else {
			$order = new Order();
			$order->create(OrderType::Free);
			$order->setPayment(array("method" => OrderPayMethod::Transfer));
			$order->setAddress($_POST['address']);
			
			foreach (OrderManager::$theater['prices'] as $key => $price) {
				if ($price['type'] == "free") break;
			}
			
			for ($i = 0; $i < $_POST['number']; $i++) {
				if (!$order->addTicket($key, $_POST['date'])) break;
			}

			$order->save();
			
			redirectTo("?");
		}
	}
	
	$_tpl->display("members/free_new.tpl");

} else {
	$result = $_db->query('SELECT id FROM orders WHERE type = ? ORDER BY lastname', array(OrderType::Free));
	$orders = array();
	while ($order = $result->fetch()) {
		$orders[] = OrderManager::getOrderById($order['id']);
	}
	
	$_tpl->assign("orders", $orders);
	$_tpl->display("members/free.tpl");
}

?>