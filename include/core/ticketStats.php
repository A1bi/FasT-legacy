<?php

loadComponent("order");

class TicketStats {
	
	public function updateForOrder($order) {
		$updated = array();
	
		foreach ($order->getTickets() as $ticket) {
			$cache = &$updated[$ticket->getDate()][$ticket->getType()][$order->getType()];
			if ($cache) continue;
			
			$this->updateForTicket($ticket, $order->getType());
			$cache = true;
		}
		
		$this->updateTotals();
	}
	
	public function getValue($date, $ticketType, $orderType, $retail = 0) {
		global $_db;
		
		$result = $_db->query('SELECT id, number, revenue FROM orders_stats WHERE date = ? AND ticketType = ? AND orderType = ? AND retail = ?', array($date, $ticketType, $orderType, $retail));
		return $result->fetch();
	}
	
	public function updateValueWithPrice($date, $ticketType, $orderType, $retail, $number, $price) {
		$this->updateValue($date, $ticketType, $orderType, $retail, $number, $number * $price);
	}
	
	private function updateValue($date, $ticketType, $orderType, $retail, $number, $revenue) {
		global $_db;
		
		$current = $this->getValue($date, $ticketType, $orderType, $retail);
		
		if (!$number) $number = 0;
		if (!$revenue) $revenue = 0;
		
		if (!$current['id']) {
			$_db->query('INSERT INTO orders_stats (date, ticketType, orderType, retail, number, revenue) VALUES (?, ?, ?, ?, ?, ?)', array($date, $ticketType, $orderType, $retail, $number, $revenue));
		
		} elseif ($current['number'] != $number) {
			$_db->query('UPDATE orders_stats SET number = ?, revenue = ? WHERE id = ?', array($number, $revenue, $current['id']));
		}
	}
	
	public function updateForTicket($ticket, $orderType) {
		$this->calculateAndUpdate($ticket->getDate(), $ticket->getType(), $ticket->getPrice(), $orderType);
	}
	
	public function updateForRetail($date, $ticketType, $orderType, $retail, $number) {
		$this->updateValueWithPrice($date, $ticketType, $orderType, $retail, $number, OrderManager::$theater['prices'][$ticketType]['prices']);
		$this->updateSubTotals($date, $ticketType, $orderType, $retail);
	}
	
	private function calculateAndUpdate($date, $ticketType, $price, $orderType) {
		global $_db;
		
		$result = $_db->query('	SELECT		COUNT(*) AS number
								FROM		orders_tickets AS t,
											orders AS o
								WHERE		t.date = ?
								AND			t.type = ?
								AND			t.cancelled = 0
								AND			t.`order` = o.id
								AND			o.type = ?
								GROUP BY	t.date',
								array($date, $ticketType, $orderType));
		$stat = $result->fetch();
		
		$this->updateValueWithPrice($date, $ticketType, $orderType, 0, $stat['number'], $price);
		
		$this->updateSubTotals($date, $ticketType, $orderType, 0);
	}
	
	private function updateSubTotals($date, $ticketType, $orderType, $retail) {
		global $_db;
		
		$result = $_db->query('	SELECT		SUM(number) AS number,
											SUM(revenue) AS revenue
								FROM		orders_stats
								WHERE		date = ?
								AND			ticketType != -1
								AND			orderType = ?
								AND			retail = ?',
								array($date, $orderType, $retail));
		$stat = $result->fetch();
		
		$this->updateValue($date, -1, $orderType, $retail, $stat['number'], $stat['revenue']);
		
		$result = $_db->query('	SELECT		SUM(number) AS number,
											SUM(revenue) AS revenue,
											ticketType
								FROM		orders_stats
								WHERE		orderType = ?
								AND			ticketType != -1
								AND			date != -1
								AND			retail = ?
								GROUP BY	ticketType',
								array($orderType, $retail));
		while ($stat = $result->fetch()) {
			$this->updateValue(-1, $stat['ticketType'], $orderType, $retail, $stat['number'], $stat['revenue']);
		}
		
		$result = $_db->query('	SELECT		SUM(number) AS number,
											SUM(revenue) AS revenue
								FROM		orders_stats
								WHERE		date = -1
								AND			orderType = ?
								AND			ticketType != -1
								AND			retail = ?',
								array($orderType, $retail));
		$stat = $result->fetch();
		
		$this->updateValue(-1, -1, $orderType, $retail, $stat['number'], $stat['revenue']);
	}
	
	public function updateTotals() {
		global $_db;
		
		$result = $_db->query('	SELECT		date,
											ticketType,
											SUM(number) AS number,
											SUM(revenue) AS revenue
								FROM		orders_stats
								WHERE		date != -1
								AND			orderType != -1
								AND			ticketType != -1
								GROUP BY	ticketType,
											date
								WITH ROLLUP');
								
		while ($stat = $result->fetch()) {
			$this->updateValue((!is_null($stat['date'])) ? $stat['date'] : -1, (!is_null($stat['ticketType'])) ? $stat['ticketType'] : -1, -1, 0, $stat['number'], $stat['revenue']);
		}
		
		$result = $_db->query('	SELECT		date,
											SUM(number) AS number,
											SUM(revenue) AS revenue
								FROM		orders_stats
								WHERE		date != -1
								AND			orderType = -1
								AND			ticketType != -1
								GROUP BY	date
								WITH ROLLUP');
								
		while ($stat = $result->fetch()) {
			$this->updateValue((!is_null($stat['date'])) ? $stat['date'] : -1, -1, -1, 0, $stat['number'], $stat['revenue']);
		}
	}
	
	public function updateAll() {
		foreach (OrderManager::$theater['dates'] as $date => $dummy) {
			foreach (OrderManager::$theater['prices'] as $ticketType => $price) {
			
				for ($i = OrderType::Online; $i < OrderType::Retail; $i++) {
					$this->calculateAndUpdate($date, $ticketType, $price['price'], $i);
				}
				
				foreach (OrderManager::$theater['retails'] as $retail => $dummy2) {
					$this->updateSubTotals($date, $ticketType, OrderType::Retail, $retail);
				}
				
			}
		}
		
		$this->updateTotals();
	}
	
}

?>