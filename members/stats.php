<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("ticketStats");

function addStat($date, $ticketType, $orderType, $retail, &$tStats, &$stats) {
	$stat = $tStats->getValue($date, $ticketType, $orderType, $retail);
	$stats[$orderType][$retail][$date][$ticketType] = array("number" => $stat['number'], "revenue" => money_format("%!.0n", $stat['revenue']));
}

if ($_GET['ajax'] && $_GET['action'] == "getStats") {	
	$tStats = new TicketStats;
	$stats = array();
	
	for ($orderType = -1; $orderType <= OrderType::Retail; $orderType++) {
		for ($date = -1; $date <= count(OrderManager::getDates()); $date++) {
			if ($date == 0) continue;
			
			foreach (OrderManager::getRetails() as $retail => $dummy) {
				if ($orderType != OrderType::Retail) $retail = -1;
				
				foreach (OrderManager::getTicketTypes($orderType) as $ticketType => $dummy) {
					addStat($date, $ticketType, $orderType, $retail, $tStats, $stats);
				}
				addStat($date, -1, $orderType, $retail, $tStats, $stats);
			}
		}
	}

	echo json_encode($stats);
	
} elseif ($_GET['action'] == "editRetail") {
	$retails = OrderManager::getRetails();
	$retail = $retails[$_GET['retail']];
	if (!$retail) redirectTo("?");
	
	$stats = new TicketStats;
	
	if ($_POST['edit']) {
		foreach (OrderManager::getTicketTypes(OrderType::Retail) as $ticketType => $dummy) {
			foreach (OrderManager::getDates() as $date => $dummy2) {
				$add = intval($_POST['number'][$date][$ticketType]);
				if ($add != 0) {
					$current = $stats->getValue($date, $ticketType, OrderType::Retail, $_GET['retail']);
					$stats->updateForRetail($date, $ticketType, $_GET['retail'], $current['number'] + $add);
				}
			}
		}
		$stats->updateTotals();
		
		redirectTo("?retail=".$_GET['retail']);
	}
	
	$_tpl->assign("stats", $stats);
	$_tpl->assign("retail", $retail);
	$_tpl->display("members/stats_edit.tpl");
	
} elseif ($_GET['action'] == "update") {
	$tStats = new TicketStats;
	$tStats->updateAll();

} else {
	$retails = array();
	foreach (OrderManager::getRetails() as $key => $retail) {
		$retails[OrderType::Retail.",".$key] = $retail;
	}
	
	$options = array(
		"options" => array(
			"Bestellungen" => array(OrderType::Online => "Online", OrderType::Manual => "Normal", OrderType::Free => "Freikarten"),
			"Vorverkaufsstellen" => $retails,
			"Gesamt" => array("-1,0" => "ohne Freikarten", "-1,1" => "mit Freikarten")
		)
	);
	if (isset($_GET['retail'])) {
		$options['selected'] = OrderType::Retail.",".$_GET['retail'];
	} else {
		$options['selected'] = "-1,1";
	}
	
	$_tpl->assign("orderTypes", $options);
	$_tpl->display("members/stats.tpl");
}
?>