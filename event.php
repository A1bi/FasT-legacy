<?php
include('./include/main.php');

loadComponent("orderManager");
loadComponent("ticketStats");

$_db = new database;
OrderManager::init();
$tStats = new TicketStats;

$dates = array();
foreach (OrderManager::getDates() as $key => $date) {
	$stat = $tStats->getValue($key, -1, -1);
	$dates[$key] = array("time" => $date, "ticketsLeft" => $stat['number'] < 300);
}

$_tpl->assign("dates", $dates);
$_tpl->display("termine_montevideo.tpl");

?>