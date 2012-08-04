<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("ticketStats");
loadComponent("orderManager");
OrderManager::init();

function addStat($date, $ticketType, $orderType, $retail, &$tStats, &$stats) {
	$stat = $tStats->getValue($date, $ticketType, $orderType, $retail);
	$stats[$orderType][$retail][$date][$ticketType] = array("number" => $stat['number'], "revenue" => $stat['revenue']);
}

if ($_GET['ajax'] && $_GET['action'] == "getStats") {	
	$tStats = new TicketStats;
	$stats = array();
	
	for ($orderType = -1; $orderType <= OrderType::Retail; $orderType++) {
		for ($date = -1; $date <= count(OrderManager::$theater['dates']); $date++) {
			if ($date == 0) continue;
			
			foreach (OrderManager::$theater['retails'] as $retail => $dummy) {
				if ($orderType != OrderType::Retail) $retail = 0;
				
				for ($ticketType = -1; $ticketType < count(OrderManager::$theater['prices']); $ticketType++) {
					addStat($date, $ticketType, $orderType, $retail, $tStats, $stats);
				}
			}
		}
	}
	
	echo json_encode($stats);
	
} elseif ($_GET['action'] == "editRetail") {
	$retail = OrderManager::$theater['retails'][$_GET['retail']];
	if (!$retail) redirectTo("?");
	
	$stats = new TicketStats;
	
	if ($_POST['edit']) {
		foreach (OrderManager::$theater['prices'] as $ticketType => $dummy) {
			foreach (OrderManager::$theater['dates'] as $date => $dummy2) {
				$stats->updateForRetail($date, $ticketType, $_GET['retail'], $_POST['number'][$date][$ticketType]);
			}
		}
		$stats->updateTotals();
		
		redirectTo("?retail=".$_GET['retail']);
	}
	
	$_tpl->assign("stats", $stats);
	$_tpl->assign("retail", $retail);
	$_tpl->display("members/stats_edit.tpl");

} else {
	$retails = array();
	foreach (OrderManager::$theater['retails'] as $key => $retail) {
		$retails[OrderType::Retail.",".$key] = $retail;
	}
	
	$options = array(
		"options" => array(
			"Bestellungen" => array(OrderType::Online => "Online", OrderType::Manual => "Telefon", OrderType::Free => "Freikarten"),
			"Vorverkaufsstellen" => $retails,
			"Gesamt" => array("-1,0" => "ohne Freikarten", "-1,1" => "mit Freikarten")
		),
		"selected" => OrderType::Retail.",".$_GET['retail']
	);
	
	$_tpl->assign("orderTypes", $options);
	$_tpl->display("members/stats.tpl");
}
?>