<?php

class OrderManager {
	
	static $instance = null;
	static $theater, $company;
	
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
	
}

class Order {
	
	private $id, $sId, $total, $hash,
			$address = array("firstname" => "", "lastname" => "", "fon" => "", "email" => ""),
			$payment = array("method" => "", "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => false),
			$tickets = array();

	public function __construct($orderInfo) {
		// check if given info is ok
		if ($this->checkInfo($orderInfo)) {
			// set info
			$this->address = $orderInfo['address'];
			$this->payment = $orderInfo['payment'];
			$this->total = $orderInfo['total'];
			$this->sId = createId(6, "orders", "sId", true);
			$this->hash = md5($this->sId);
			
			$this->save();
			
			$this->createTickets($orderInfo['number'], $orderInfo['date']);
		} else {
			return null;
		}
	}
	
	private function checkInfo($orderInfo) {
		// check date
		if (empty(OrderManager::$theater['dates'][$orderInfo['date']])) {
			return false;
		}

		// check numbers and total
		$total = 0;
		foreach (OrderManager::$theater['prices'] as $type => $price) {
			$total += $orderInfo['number'][$type]*$price;
		}
		if ($total == 0 || $total != $orderInfo['total']) {
			return false;
		}

		// check address
		foreach ($this->address as $key => $value) {
			if (empty($orderInfo['address'][$key]) || ($key == "email" && !preg_match("#^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z]){2,9}$#", $orderInfo['address'][$key]))) {
				return false;
			}
		}

		// check payment
		if ($orderInfo['payment']['method'] != "transfer") {
			foreach ($this->payment as $key => $value) {
				if (empty($orderInfo['payment'][$key])) {
					return false;
				}
			}
		}

		// check if accepted TOS
		if (!$orderInfo['accepted']) {
			return false;
		}
		
		return true;
	}
	
	private function getMailHeaders() {
		$headers = sprintf("From:%s<%s>\n", mb_encode_mimeheader(OrderManager::$company['name'], "UTF-8", "Q"), OrderManager::$company['noreply']);
		$headers .= sprintf("Reply-To:%s\n", OrderManager::$company['email']);
		$headers .= "Mime-Version: 1.0 Content-Type: text/plain; charset=utf-8 Content-Transfer-Encoding: quoted-printable";
		
		return $headers;
	}
	
	public function mailConfirmation() {
		global $_tpl;
		
		$_tpl->assign("order", $this);
		$_tpl->assign("address", $this->address);
		$_tpl->assign("tickets", $this->tickets);
		$_tpl->assign("payment", $this->payment);

		$body = $_tpl->fetch("order_mail_confirmation.tpl");

		@mail($this->address['email'], "Ihre Bestellung", $body, $this->getMailHeaders());
	}
	
	public function mailTickets() {
		global $_tpl;
		
		$_tpl->assign("address", $this->address);
		$_tpl->assign("hash", $this->hash);
		
		$body = $_tpl->fetch("order_mail_tickets.tpl");

		@mail($this->address['email'], "Ihre Karten", $body, $this->getMailHeaders());
	}
	
	private function createTickets($numbers, $date) {
		$t = 0;
		foreach (OrderManager::$theater['prices'] as $type => $val) {
			for ($i = 0; $i < $numbers[$type]; $i++) {
				$this->tickets[] = new Ticket($t, $date, $this);
			}
			$t++;
		}
	}
	
	public function createPdf() {
		global $_tpl;
		require('/usr/share/php/fpdf/fpdf.php');

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
				$pdf->Cell(50, 9, ($ticket->getType()) ? "Erwachsener" : utf8_decode("Ermäßigt"), 0, 2);
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

		$pdf->Output("./media/tickets/".$this->hash.".pdf");
	}

	private function save() {
		global $_db;
		
		$_db->query('INSERT INTO orders VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, "")',
				array($this->getSId(), $this->address['firstname'], $this->address['lastname'], $this->address['fon'], $this->address['email'], $this->payment['method'], $this->payment['name'], $this->payment['number'], $this->payment['blz'], $this->payment['bank'], $this->getTotal(), time(), $_SERVER['REMOTE_ADDR']));
		
		$this->id = $_db->id();
	}
	
	public function cancel($reason) {
		global $_db;
		
		// cancel order in db
		$_db->query('UPDATE orders SET cancelled = 1, cancelReason = ? WHERE id = ?', array($reason, $this->id));
		
		// cancel each ticket
		foreach ($this->tickets as $ticket) {
			$ticket->cancel($reason);
		}
	}
	
	public function getDate() {
		return $this->date;
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
	
	public function getAddress() {
		return $this->address;
	}
	
	public function getPayment() {
		return $this->payment;
	}

}

class Ticket {
	
	private $id, $sId, $date, $type, $order;
	
	public function __construct($type, $date, &$order) {
		$this->type = $type;
		$this->date = $date;
		$this->order = $order;
		
		$this->save();
	}
	
	private function save() {
		global $_db;
		
		$this->sId = createId(6, "orders_tickets", "sId", true);
		$_db->query('INSERT INTO orders_tickets VALUES (null, ?, ?, ?, ?, 0, "", 0)', array($this->sId, $this->order->getId(), $this->date, $this->type));
		$this->id = $_db->id();
	}
	
	public function cancel($reason) {
		global $_db;
		
		$_db->query('UPDATE orders_tickets SET cancelled = 1, cancelReason = ? WHERE id = ?', array($reason, $this->id));
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
	
	public function getType() {
		return $this->type;
	}

	public function getDateString() {
		return OrderManager::getStringForDate(OrderManager::$theater['dates'][$this->date]);
	}
	
	public function getPrice() {
		return OrderManager::$theater['prices'][($this->type) ? "adults" : "kids"];
	}
}

?>
