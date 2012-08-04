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

} else {
	$retails = array();
	foreach (OrderManager::$theater['retails'] as $key => $retail) {
		$retails[OrderType::Retail.",".$key] = $retail;
	}
	
	$options = array(
		"Bestellungen" => array(OrderType::Online => "Online", OrderType::Manual => "Telefon", OrderType::Free => "Freikarten"),
		"Vorverkaufsstellen" => $retails,
		"Gesamt" => array("-1,0" => "ohne Freikarten", "-1,1" => "mit Freikarten")
	);
	
	$_tpl->assign("orderTypes", $options);
	$_tpl->display("members/stats.tpl");
}
?>