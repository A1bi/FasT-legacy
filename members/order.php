<?php
include('../include/members.php');

limitAccess(array(2));

$order = OrderManager::getInstance()->getOrderById($_GET['id']);
// not found ?
if (!$order) redirectTo("/");

if (!empty($_GET['action'])) {
	loadComponent("queue");
	$queue = new Queue();
	
	switch ($_GET['action']) {
		case "markPaid":
			if ($order->markPaid()) {
				if ($order->getType() != OrderType::Online) break;
				
				$payment = $order->getPayment();
				if ($payment['method'] == OrderPayMethod::Transfer) {
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
			
		case "delete":
			if ($order->getType() == OrderType::Free) {
				$order->delete();
			}
			redirectTo("freikarten");
			break;
			
		case "approve":
			$order->approve($_GET['undo'] ? false : true);
			break;
			
		case "sendPayReminder":
			$order->mailPayReminder();
			break;
			
		case "edit":
			if ($_POST['edit']) {
				$order->setCategory($_POST['category']);
				$order->setAddress($_POST['address']);
				$order->setPayment($_POST['payment']);
				$order->setNotes($_POST['notes']);
				$order->save();
			}
			break;
			
	}
	
	if ($_GET['goto'] == "orders") {
		redirectTo("buchungen");
	} else {
		redirectTo("?");
	}

} else {
	$_tpl->assign("order", $order);
	$_tpl->display("members/order.tpl");
}

?>