<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("ticketStats");
loadComponent("orderManager");
OrderManager::init();

function addStat($date, $ticketType, $orderType, &$tStats, &$stats) {
	$stat = $tStats->getValue($date, $ticketType, $orderType);
	$stats[$orderType][$date][$ticketType] = array("number" => $stat['number'], "revenue" => $stat['revenue']);
}

if ($_GET['ajax'] && $_GET['action'] == "getStats") {	
	$tStats = new TicketStats;
	$stats = array();
	
	for ($orderType = -1; $orderType <= OrderType::Retail; $orderType++) {
		for ($date = -1; $date <= count(OrderManager::$theater['dates']); $date++) {
			if ($date == 0) continue;
			
			for ($ticketType = -1; $ticketType < count(OrderManager::$theater['prices']); $ticketType++) {
				addStat($date, $ticketType, $orderType, $tStats, $stats);
			}
		}
	}
	
	echo json_encode($stats);

} else {
	$options = array(
		"Bestellungen" => array(0 => "Online", 1 => "Telefon", 2 => "Freikarten"),
		"Vorverkaufsstellen" => array("3,1" => "Sportstudio"),
		"Gesamt" => array("-1,0" => "ohne Freikarten", "-1,1" => "mit Freikarten")
	);
	
	$_tpl->assign("orderTypes", $options);
	$_tpl->display("members/stats.tpl");
}
?>