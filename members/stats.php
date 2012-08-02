<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orderManager");
OrderManager::init();

$stats = new TicketStats;
$_tpl->assign("stats", $stats);

$_tpl->display("members/stats.tpl");

?>