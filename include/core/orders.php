<?php

class OrderStatus {
	const Placed = 0, WaitingForApproval = 1, WaitingForPayment = 2, Approved = 3, Finished = 4, Cancelled = 5;
}

class OrderEvent {
	const Placed = 0, Approved = 1, Disapproved = 2, MarkedAsPaid = 3, Charged = 4, Cancelled = 5, CancelledTicket = 6, SentTickets = 7, SentPayReminder = 8;
}

class OrderPayMethod {
	const Charge = 1, Transfer = 2;
}

class OrderManager {
	
	static $instance = null;
	static $theater, $company;
	private $orders = array();
	
	private function __construct() {
		self::$theater = getData("theater_montevideo");
		self::$company = getData("company");
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
		
		if (!$this->tickets[$id]) {
			// get info for order from db
			$result = $_db->query('SELECT * FROM orders WHERE id = ?', array($id));
			$orderInfo = $result->fetch();
			if ($orderInfo['id']) {
				// get tickets from db
				$result = $_db->query('SELECT * FROM orders_tickets WHERE `order` = ?', array($id));
				$orderInfo['tickets'] = $result->fetchAll();
				
				// create order instance
				$order = new Order();
				$order->init($orderInfo);
				$this->tickets[$order->getId()] = $order;
				return $order;
			}
			
			return null;
			
		} else {
			return $this->tickets[$id];
		}
	}
}

class Order {
	
	private $id, $sId, $total = 0, $hash, $time, $status = OrderStatus::Placed, $paid = false,
			$address = array("gender" => 0, "firstname" => "", "lastname" => "", "plz" => 0, "fon" => "", "email" => ""),
			$payment = array("method" => 0, "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => true),
			$cancelled = array("cancelled" => false, "reason" => ""),
			$tickets = array(),
			$events = NULL;
	
	public function create() {
		// set info
		$this->sId = createId(6, "orders", "sId", true);
		$this->hash = md5($this->sId);
	}
	
	public function init($orderInfo) {
		// set info from db
		$this->id = $orderInfo['id'];
		$this->sId = $orderInfo['sId'];
		$this->time = $orderInfo['time'];
		$this->total = $orderInfo['total'];
		$this->status = $orderInfo['status'];
		$this->paid = $orderInfo['paid'];
		$this->hash = md5($this->sId);
		
		// address
		$this->address = array();
		$addressFields = array("gender", "firstname", "lastname", "plz", "fon", "email");
		foreach ($addressFields as $field) {
			$this->address[$field] = $orderInfo[$field];
		}
		
		// payment
		$this->payment = array("method" => $orderInfo['payMethod'], "name" => $orderInfo['kName'], "number" => $orderInfo['kNo'], "blz" => $orderInfo['blz'], "bank" => $orderInfo['bank']);
		
		// cancelled
		$this->cancelled = array("cancelled" => $orderInfo['cancelled'], "reason" => $orderInfo['cancelReason']);
		
		// tickets
		$this->initTickets($orderInfo['tickets']);
	}
	
	private function isInt($val) {
		return (ctype_digit((string)$val) && is_int(intval($val)));
	}
	
	public function mailConfirmation() {
		$this->mail("Ihre Bestellung", "confirmation");
	}
	
	public function mailTickets() {
		global $_tpl;
		
		$_tpl->assign("gotPaid", $this->payment['method'] == OrderPayMethod::Transfer && $this->paid);
		
		$this->mail("Ihre Karten", "tickets");
		
		$this->logEvent(OrderEvent::SentTickets);
	}
	
	public function mailPayReminder() {
		$this->mail("Zahlungserinnerung", "payreminder");
		
		$this->logEvent(OrderEvent::SentPayReminder);
	}
	
	public function mailCancellation() {
		global $_tpl;
		
		$_tpl->assign("reason", $this->cancelled['reason']);
		
		$this->mail("Stornierung", "cancellation");
	}
	
	public function mail($subject, $tpl) {
		global $_tpl;
		require_once("/usr/share/php/libphp-phpmailer/class.phpmailer.php");
		
		$_tpl->assign("order", $this);
		$_tpl->assign("address", $this->address);
		
		$mail = new PHPMailer();
		$mail->CharSet = 'utf-8';
		
		$mail->SetFrom(OrderManager::$company['noreply'], OrderManager::$company['name']);
		$mail->ClearReplyTos();
		$mail->AddReplyTo(OrderManager::$company['email'], OrderManager::$company['name']);
		$mail->AddAddress($this->address['email']);
		
		$mail->Subject = $subject;
		$mail->Body = $_tpl->fetch("order_mail_" . $tpl . ".tpl");
		
		$mail->Send();
	}
	
	public function addTicket($type, $date) {
		$ticket = new Ticket();
		$ticket->create();
		$ticket->setOrder($this);
		if (!$ticket->setDate($date) || !$ticket->setType($type)) return false;
		
		$this->tickets[] = $ticket;
		$this->updateTotal();
		
		return true;
	}
	
	private function initTickets($tickets) {
		if (is_numeric($tickets)) {
			// only number of tickets is given (for perfomance reasons) so we have to fake an array of tickets
			for ($i = 0; $i < $tickets; $i++) {
				$this->tickets[] = null;
			}
		
		} else if (is_array($tickets)) {
			foreach ($tickets as $ticketInfo) {
				$ticket = new Ticket();
				$ticket->setOrder($this);
				$ticket->init($ticketInfo);
				$this->tickets[] = $ticket;
			}
		}
	}
	
	public function createPdf() {
		global $_tpl;
		require_once('/usr/share/php/fpdf/fpdf.php');

		$pdf = new FPDF();
		$pdf->SetTitle("Eintrittskarten - " . OrderManager::$theater['title'], true);
		$pdf->SetAuthor(OrderManager::$company['name'], true);
		
		$pdf->AddFont("Qlassik", "", "Qlassik_TB.php");
		$pdf->AddFont("Code39", "", "code39.php");
		
		$pdf->AddPage();
		$pdf->SetAutoPageBreak(false);

		$width = 120;
		$height = 60;
		$ticketHeight = $height+1;

		$nTickets = count($this->tickets);
		$ticketsOnPage = 0;
		
		for ($i = 0; $i <= $nTickets; $i++) {
			
			$orX = 15;
			$orY = 15 + $ticketsOnPage * ($ticketHeight + 7);

			if ($orY + $ticketHeight > 280) {
				$pdf->AddPage();
				$ticketsOnPage = 0;
				$orY = 15;
			}
			
			if ($i == $nTickets) {
				// disclaimer on the bottom
				$pdf->SetX(15);
				$pdf->SetY($pdf->GetY() + 15);
				$pdf->SetFont("Helvetica", "", "18");
				$pdf->Cell(50, 10, "Hinweise:", 0, 2);
				$pdf->SetFont("Helvetica", "", "10");
				$pdf->MultiCell(0, 6, utf8_decode($_tpl->fetch("order_pdf_disclaimer.tpl")), 0, 2);
			
			} else {
				$ticket = $this->tickets[$i];
				if ($ticket->isCancelled()) continue;
				
				$ticketsOnPage++;

				// frame
				$pdf->SetLineWidth(0.5);
				$pdf->Rect($orX, $orY, $width, $height, "D");
				$orX += 0.5 + 2;
				$orY += 0.5 + 2;

				$pdf->Image("./gfx/logo.png", $orX + 85, $orY, 28, 0, "PNG");

				$pdf->SetXY($orX + 3, $orY + 2);
				$pdf->SetFont("Qlassik", "", "25");
				$pdf->Cell(50, 10, "Eintrittskarte", 0, 2);
				$pdf->SetFont("Qlassik", "", "18");
				$pdf->Cell(50, 9, utf8_decode($ticket->getDesc()), 0, 2);
				$pdf->SetXY($pdf->GetX(), $pdf->GetY() + 2);
				$pdf->SetFont("Helvetica", "", "11");
				$pdf->Cell(22, 6, utf8_decode("Aufführung:"), 0, 0);
				$pdf->Cell(28, 6, utf8_decode(OrderManager::$theater['title']), 0, 2);
				$pdf->Cell(28, 6, utf8_decode($ticket->getDateString()), 0, 2);
				$pdf->SetFont("Helvetica", "", "18");
				$pdf->SetXY($orX + 8, $pdf->GetY() + 3);
				$pdf->Cell(50, 7, $ticket->getPrice().",00 ".chr(128), 0, 2);

				$pdf->SetLineWidth(0.3);
				$pdf->Line($orX + 1, $orY + 48, $orX + 41, $orY + 48);

				$pdf->SetXY($orX + 1, $orY + 44);
				$pdf->SetFont("Helvetica", "", "9");
				$pdf->Cell(42, 16, sprintf("TN: %s | ON: %s", $ticket->getSId(), $this->sId), 0, 0);

				$pdf->SetFont("Code39", "", "32");
				$pdf->Cell(50, 10, sprintf("*T%sO%s*", $ticket->getSId(), $this->sId), 0, 2);
			}

		}

		$file = "./media/tickets/".$this->hash.".pdf";
		$pdf->Output($file);
		chmod($file, 0777);
	}

	public function save() {
		global $_db;
		
		// initial status
		if ($this->payment['method'] == OrderPayMethod::Charge) {
			$status = OrderStatus::WaitingForApproval;
		} else {
			$status = OrderStatus::WaitingForPayment;
		}
		
		// save everything to db
		$_db->query('INSERT INTO orders VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, 0, "")',
				array($this->getSId(), $this->address['gender'], $this->address['firstname'], $this->address['lastname'], $this->address['plz'], $this->address['fon'], $this->address['email'], $this->payment['method'], $this->payment['name'], $this->payment['number'], $this->payment['blz'], $this->payment['bank'], $this->getTotal(), time(), $_SERVER['REMOTE_ADDR'], $status));
		
		$this->id = $_db->id();
		
		// save tickets too
		foreach ($this->tickets as $ticket) {
			$ticket->save();
		}
		
		$this->logEvent(OrderEvent::Placed);
	}
	
	private function updateTotal() {
		$this->total = 0;
	
		$tickets = $this->getTickets();
		foreach ($tickets as $ticket) {
			$this->total += $ticket->getPrice();
		}
	}
	
	public function cancel($reason) {
		global $_db;
		
		if ($this->isCancelled()) return;
		
		$this->cancelled['cancelled'] = true;
		$this->cancelled['reason'] = $reason;
		$this->status = OrderStatus::Cancelled;
		
		// cancel order in db
		$_db->query('UPDATE orders SET cancelled = 1, status = ?, cancelReason = ? WHERE id = ?', array($this->status, $reason, $this->id));
		
		// cancel each ticket
		foreach ($this->tickets as $ticket) {
			$ticket->cancel($reason);
		}
		
		$this->mailCancellation();
		
		$this->logEvent(OrderEvent::Cancelled);
		
		return true;
	}
	
	private function logEvent($event, $info = "") {
		global $_db, $_user;
		
		$_db->query('INSERT INTO orders_events VALUES (null, ?, ?, ?, ?, ?)', array($this->id, $event, $info, $_user['id'], time()));
	}
	
	public function getEvents() {
		global $_db;
	
		if (!$this->events) {
			$result = $_db->query('	SELECT		e.*,
												u.realname
									FROM		orders_events AS e
									LEFT JOIN	users AS u
									ON			u.id = e.user
									WHERE		e.`order` = ?
									ORDER BY	e.id DESC
									', array($this->id));
			$this->events = $result->fetchAll();
		}
		
		return $this->events;
	}
	
	public function getTime() {
		return $this->time;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function getSId() {
		return $this->sId;
	}
	
	public function getTotal() {
		return $this->total;
	}
	
	public function setAddress($address) {
		// check address
		foreach ($this->address as $key => $value) {
			if (empty($address[$key])) {
				return false;
			}
		}
		
		if (!$this->isInt($address['plz']) || strlen($address['plz']) != 5) return false;
		if (!preg_match("#^([a-z0-9-]+\.?)+@([a-z0-9-]+\.)+[a-z]{2,9}$#i", $address['email'])) return false;
		
		$this->address = $address;
		$this->address['gender'] = ($address['gender'] == 2) ? 2 : 1;
		
		return true;
	}
	
	public function getAddress() {
		return $this->address;
	}
	
	public function setPayment($payment) {
		// check payment
		if ($payment['method'] == OrderPayMethod::Charge) {
			foreach ($this->payment as $key => $value) {
				if (empty($payment[$key])) {
					return false;
				}
			}
			
			if (!$this->isInt($payment['number'])) return false;
			if (!$this->isInt($payment['blz']) || strlen($payment['blz']) != 8) return false;
			
		} else if ($payment['method'] != OrderPayMethod::Transfer) {
			// none of the known pay methods given
			return false;
		}
		
		$this->payment = $payment;
		
		return true;
	}
	
	public function getPayment() {
		return $this->payment;
	}
	
	public function getTickets() {
		return $this->tickets;
	}
	
	public function getNumberOfValidTickets() {
		$number = 0;
		foreach ($this->tickets as $ticket) {
			if (!$ticket->isCancelled()) $number++;
		}
		
		return $number;
	}
	
	public function isCancelled() {
		return $this->cancelled['cancelled'];
	}
	
	public function getCancelReason() {
		return $this->cancelled['reason'];
	}
	
	public function getStatus() {
		return $this->status;
	}
	
	public function getHash() {
		return $this->hash;
	}
	
	public function isPaid() {
		return $this->paid;
	}
	
	private function setStatus($status) {
		global $_db;
		
		$this->status = $status;
		$_db->query('UPDATE orders SET status = ? WHERE id = ?', array($status, $this->id));
	}
	
	public function approve($toggle = true) {
		if ($toggle) {
			$status = OrderStatus::Approved;
			$event = OrderEvent::Approved;
		} else {
			$status = OrderStatus::WaitingForApproval;
			$event = OrderEvent::Disapproved;
		}
		if ($this->status == $status) return;
		
		$this->setStatus($status);
		
		$this->logEvent($event);
		
		return true;
	}
	
	public function markPaid($log = true) {
		global $_db;
		
		if ($this->paid) return;
		
		$this->paid = true;
		$_db->query('UPDATE orders SET paid = 1 WHERE id = ?', array($this->id));
		$this->setStatus(OrderStatus::Finished);
		
		if ($log) $this->logEvent(OrderEvent::MarkedAsPaid);
		
		return true;
	}
	
	public function charge($id) {
		global $_db;
		
		$this->markPaid(false);
		$_db->query('UPDATE orders SET charge = ? WHERE id = ?', array($id, $this->id));
		
		$this->logEvent(OrderEvent::Charged);
	}

}

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
