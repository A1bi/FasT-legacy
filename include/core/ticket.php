<?php

loadComponent("orderManager");
loadComponent("order");

class Ticket {
	
	private $id, $sId, $date, $type, $voided, $order;
	
	public function create() {
		$this->sId = createId(6, "orders_tickets", "sId", true);
	}
	
	public function init($info) {
		$this->id = $info['id'];
		$this->sId = $info['sId'];
		$this->date = $info['date'];
		$this->type = $info['type'];
		$this->voided = $info['voided'];
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
	
	public function delete() {
		global $_db;
		
		if ($this->id) {
			$_db->query('DELETE FROM orders_tickets WHERE id = ?', array($this->id));
		}
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
		$types = OrderManager::getTicketTypes();
		if (!$types[$type]) return false;
		
		$this->type = $type;
		
		return true;
	}
	
	public function getType() {
		return $this->type;
	}
	
	public function getVoided() {
		return $this->voided;
	}
	
	public function setDate($date) {
		$dates = OrderManager::getDates();
		if (!$dates[$date]) return false;
		
		$this->date = $date;
		
		return true;
	}
	
	public function getDate() {
		return $this->date;
	}

	public function getDateString() {
		$dates = OrderManager::getDates();
		return OrderManager::getStringForDate($dates[$this->date]);
	}
	
	public function getPrice() {
		$types = OrderManager::getTicketTypes();
		return $types[$this->type]['price'];
	}
	
	public function getDesc() {
		$types = OrderManager::getTicketTypes();
		return $types[$this->type]['desc'];
	}
	
	public function isCancelled() {
		return $this->cancelled['cancelled'];
	}
	
	public function getCancelReason() {
		return $this->cancelled['reason'];
	}
}

?>