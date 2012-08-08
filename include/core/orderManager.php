<?php

loadComponent("order");

class OrderManager {
	
	private static $instance = null;
	private static $event, $company;
	private static $orders = array();
	
	private function __construct() {
		self::$event = getData("theater_montevideo");
		self::$company = getData("company");
		
		// add free ticket type
		self::$event['ticketTypes'][] = array(
			"type" => "free",
			"price" => 0,
			"desc" => "Freikarte"
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
	
	static function getCategories() {
		global $_db;
		
		$result = $_db->query('SELECT * FROM orders_categories ORDER BY name');
		
		$cats = array(0 => "");
		while ($cat = $result->fetch()) {
			$cats[$cat['id']] = $cat['name'];
		}
		
		return $cats;
	}
	
	static function getDates() {
		return self::$event['dates'];
	}
	
	static function getTicketTypes() {
		return self::$event['ticketTypes'];
	}
	
	static function getRetails() {
		return self::$event['retails'];
	}
	
	static function getTitle() {
		return self::$event['title'];
	}
}

?>
