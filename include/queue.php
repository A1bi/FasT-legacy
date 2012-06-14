<?php
include("./include/main.php");

$args = getopt("", array("domain:", "batch:"));
$_SERVER['SERVER_NAME'] = $args['domain'];

$_db = new database();
loadComponent("queue");
loadComponent("orders");
$orderManager = OrderManager::getInstance();

$queue = new Queue();
$queue->setBatch($args['batch']);

$jobs = $queue->getJobs();
foreach ($jobs as $job) {
	$order = $orderManager->getOrderById($job['additional']);

	switch ($job['job']) {
		
		case "mailConfirmation":
			$order->mailConfirmation();
			break;
			
		case "createPdf":
			$order->createPdf();
			break;
		
		case "mailTickets":
			$order->mailTickets();
			break;
	}
	
	$queue->finishJob($job['id']);
}
?>