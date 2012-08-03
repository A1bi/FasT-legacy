<?php

loadComponent("order");

class OrderManager {
	
	static $instance = null;
	static $theater, $company;
	static $orders = array();
	
	private function __construct() {
		self::$theater = getData("theater_montevideo");
		self::$company = getData("company");
		
		// add free ticket type
		self::$theater['prices'][] = array(
			"type" => "free",
			"price" => 0
		);
	}
	
	static function init() {
		self::$instance = new OrderManager();
	}
	
	static function getInstance() {
		if (!self::$instance) self::init();
		
		return self::$instance;
	}
	
	static function getStringForDate($date) {
		return strftime("%A, den %d. %B um %H Uhr", $date);
	}
	
	public function getOrderById($id) {
		global $_db;
		
		if (!self::$orders[$id]) {
			// get info for order from db
			$result = $_db->query('SELECT *, UNIX_TIMESTAMP(time) AS time FROM orders WHERE id = ?', array($id));
			$orderInfo = $result->fetch();
			if ($orderInfo['id']) {
				// create order instance
				$order = new Order();
				$order->init($orderInfo);
				self::$orders[$order->getId()] = $order;
			
			} else {
				self::$orders[$id] = null;
			}
		}
		
		return self::$orders[$id];
	}
}

?>
