<?php

loadComponent("orderManager");
loadComponent("order");

class Ticket {
	
	private $id, $sId, $date, $type, $order;
	
	public function create() {
		$this->sId = createId(6, "orders_tickets", "sId", true);
	}
	
	public function init($info) {
		$this->id = $info['id'];
		$this->sId = $info['sId'];
		$this->date = $info['date'];
		$this->type = $info['type'];
		$this->cancelled = array("cancelled" => $info['cancelled'], "reason" => $info['cancelReason']);
	}
	
	public function save() {
		global $_db;
		
		$_db->query('INSERT INTO orders_tickets VALUES (null, ?, ?, ?, ?, 0, "", 0)', array($this->sId, $this->order->getId(), $this->date, $this->type));
		$this->id = $_db->id();
	}
	
	public function cancel($reason) {
		global $_db;
		
		$_db->query('UPDATE orders_tickets SET cancelled = 1, cancelReason = ? WHERE id = ?', array($reason, $this->id));
	}
	
	public function setOrder(&$order) {
		$this->order = $order;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
		$this->hash = md5($id);
	}
	
	public function getSId() {
		return $this->sId;
	}
	
	public function setType($type) {
		if (!OrderManager::$theater['prices'][$type]) return false;
		
		$this->type = $type;
		
		return true;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function setDate($date) {
		if (!OrderManager::$theater['dates'][$date]) return false;
		
		$this->date = $date;
		
		return true;
	}

	public function getDateString() {
		return OrderManager::getStringForDate(OrderManager::$theater['dates'][$this->date]);
	}
	
	public function getPrice() {
		return OrderManager::$theater['prices'][$this->type]['price'];
	}
	
	public function getDesc() {
		return OrderManager::$theater['prices'][$this->type]['desc'];
	}
	
	public function isCancelled() {
		return $this->cancelled['cancelled'];
	}
	
	public function getCancelReason() {
		return $this->cancelled['reason'];
	}
}

?>