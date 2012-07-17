<?php
include('../include/members.php');

limitAccess(array(2));

loadComponent("orders");
OrderManager::init();

function getOrdersFromInfo($orders) {
	$tmpOrders = array();
	foreach ($orders as $orderInfo) {
		$order = new Order();
		$order->init($orderInfo);
		$tmpOrders[] = $order;
	}
	
	return $tmpOrders;
}

if (!empty($_GET['order'])) {
	$order = OrderManager::getInstance()->getOrderById($_GET['order']);
	// not found ?
	if (!$order->getId()) {
		redirectTo("/mitglieder/tickets");
	}
	
	if ($_GET['action'] == "showDetails") {
		$_tpl->assign("order", $order);
		$_tpl->display("members_tickets_details.tpl");
			
	} else {
		loadComponent("queue");
		$queue = new Queue();
		
		switch ($_GET['action']) {
			case "markPaid":
				if ($order->markPaid()) {
					$payment = $order->getPayment();
					if ($payment['method'] == "transfer") {
						$queue->beginNewBatch();
						$queue->addJob("createPdf", $order->getId());
						$queue->addJob("mailTickets", $order->getId());
						$queue->exec("./include/queue");
					}
				}
				break;
				
			case "cancel":
				$order->cancel($_POST['reason']);
				break;
				
			case "approve":
				$order->approve($_GET['undo'] ? false : true);
				break;
		}
		
		redirectTo("/mitglieder/tickets" . (($_GET['goto'] != "overview") ? "?action=showDetails&order=".$order->getId() : ""));
	}

} elseif ($_GET['action'] == "getChargesSheet") {
	$file = "./media/charges/" . $_GET['id'] . ".pdf";
	if (file_exists($file)) {
		header('Content-type: application/pdf');
		readfile($file);
	} else {
		redirectTo("?");
	}

} elseif ($_GET['action'] == "charge") {
	
	$result = $_db->query('SELECT id, sId, total, kName, kNo, blz, bank FROM orders WHERE status = ?', array(OrderStatus::Approved));
	$charges = $result->fetchAll();
	$nCharges = count($charges);
	
	if ($nCharges) {
		$chargeInfo = getData("charge");
	
		$_db->query('INSERT INTO orders_charges VALUES(null, ?)', array(time()));
		$chargeId = $_db->id();
		
		loadComponent("dtaus");
		$dta = new DTAUS("LK", $chargeInfo['sender'], $chargeId);
		
		foreach ($charges as $charge) {
			$order = new Order();
			$order->init($charge);
			$payment = $order->getPayment();
			$paymentDetails = array("name" => $payment['name'], "account" => $payment['number'], "blz" => $payment['blz'], "bank" => $payment['bank']);
	
			$references = $chargeInfo['references'];
			$references[] = "ON".$order->getSId();
			$transaction = new Transaction("charge", $order->getTotal(), $paymentDetails, $references);
			$dta->addTransaction($transaction);
			
			$order->markPaid($chargeId);
		}
		
		// send mail to bank containing the dta file
		require("/usr/share/php/libphp-phpmailer/class.phpmailer.php");
		
		$dta->generateData();
		
		$mail = new PHPMailer();
		$mail->CharSet = 'utf-8';
		
		$mail->SetFrom(OrderManager::$company['email'], OrderManager::$company['name']);
		// don't send anything to the bank in dev mode
		$mail->AddAddress(($_config['dev']) ? "a1bi@me.com" : $chargeInfo['bankEmail']);
		
		$mail->Subject = "DTA-Datei";
		$mail->Body = $_tpl->fetch("members_tickets_mail_bank.tpl");
		
		// save temporary dta file
		$tmpFile = "/tmp/dta.txt";
		$file = fopen($tmpFile, "w");
		fwrite($file, $dta->getData());
		fclose($file);
		$mail->addAttachment($tmpFile, "DTAUS0.txt");
		
		$mail->Send();
		unlink($tmpFile);
		
		// create data sheet
		require('/usr/share/php/fpdf/fpdf.php');
		
		$pdf = new FPDF();
		$pdf->SetTitle("DTA-Begleitzettel", true);
		$pdf->SetAuthor(OrderManager::$company['name'], true);
		$pdf->SetMargins(15, 15);
		$pdf->AddPage();
		
		$pdf->SetFont("Arial", "B", "16");
		$pdf->MultiCell(0, 8, utf8_decode("DTA-Begleitzettel\nBelegloser Datenträgeraustausch"), 0, 2);
		$pdf->SetFont("Arial", "", "16");
		$pdf->Ln(3);
		$pdf->Cell(0, 8, utf8_decode("Sammeleinzugsauftrag"), 0, 2);
		$pdf->Ln(15);
		$pdf->SetFontSize(13);
		$pdf->Cell(0, 8, utf8_decode("Datei bereits per e-mail übermittelt!"), 0, 2);
		
		$date = date("d.m.Y");
		$sums = $dta->getSums();
		$sums['total'] = number_format($sums['total'] / 100, 2, ",", ".");
		$infos = array(
			array("Referenznummer", $chargeId),
			array("Erstellungsdatum", $date),
			array("Ausführungsdatum", $date),
			array("Anzahl der Datensätze C", $nCharges),
			array("Summe der Beträge in EUR", $sums['total']),
			array("Kontrollsumme Kontonummern", $sums['account']),
			array("Kontrollsumme Bankleitzahlen", $sums['blz']),
			array("Auftraggeber", $chargeInfo['sender']['name']),
			array("Beauftragtes Bankinstitut", $chargeInfo['sender']['bank']),
			array("Bankleitzahl", $chargeInfo['sender']['blz']),
			array("Kontonummer", $chargeInfo['sender']['account'])
		);
		
		$pdf->Ln(10);
		$pdf->SetFontSize(12);
		foreach ($infos as $info) {
			$pdf->Cell(85, 7, utf8_decode($info[0]) . ":", 0, 0);
			$pdf->Cell(80, 5, utf8_decode($info[1]), 0, 2);
			$pdf->Ln();
		}
		
		$pdf->Ln(60);
		$pdf->SetFontSize(11);
		$pdf->Cell(0, 9, $chargeInfo['sender']['location'] . ", den " . $date, 0, 2);
		$pdf->SetFontSize(12);
		$pdf->Line($pdf->GetX(), $pdf->GetY(), $pdf->GetX() + 140, $pdf->GetY());
		$pdf->Cell(0, 8, "Ort, Datum, Unterschrift");
		
		$pdf->Output("./media/charges/" . $chargeId . ".pdf");
		
		redirectTo("?action=getChargesSheet&id=" . $chargeId);
	}
	
	redirectTo("/mitglieder/tickets");


// nothing chosen -> show overview
} else {
	// statistics
	$result = $_db->query('SELECT date, type, COUNT(*) as number FROM orders_tickets WHERE cancelled = 0 GROUP BY date, type WITH ROLLUP');
	$ticketData = $result->fetchAll();
	
	$stats = array("total" => array());
	foreach ($ticketData as $ticket) {
		if ($ticket['date'] == "") {
			$arr = &$stats['total']['sum'];
		} else {
			$dateArr = &$stats['dates'][$ticket['date']];
			if ($ticket['type'] != "") {
				$arr = &$dateArr['types'][$ticket['type']];
				$stats['total']['types'][$ticket['type']] += $ticket['number'];
			} else {
				$arr = &$dateArr['sum'];
			}
		}
		
		$arr = $ticket['number'];
	}
	
	// calculate revenue
	foreach ($stats['dates'] as $key => $date) {
		$sum = 0;
		foreach (OrderManager::$theater['prices'] as $key2 => $price) {
			$sum += $price * $date['types'][(($key2 == "kids") ? 0 : 1)];
		}
		$stats['dates'][$key]['revenue'] = $sum;
		$stats['total']['revenue'] += $sum;
	}
	
	$_tpl->assign("stats", $stats);
	
	
	// orders to check
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(t.id) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status = ?
							AND			t.order = o.id
							AND			t.cancelled = 0
							GROUP BY	o.id
							ORDER BY	o.sId ASC
							', array(OrderStatus::WaitingForApproval));
	$_tpl->assign("ordersCheck", getOrdersFromInfo($result->fetchAll()));
	
	
	// orders to pay
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(t.id) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status = ?
							AND			t.order = o.id
							AND			t.cancelled = 0
							GROUP BY	o.id
							ORDER BY	o.sId ASC
							', array(OrderStatus::WaitingForPayment));
	$_tpl->assign("ordersPay", getOrdersFromInfo($result->fetchAll()));
	
	
	// finished or closed orders
	$result = $_db->query('	SELECT		o.id,
										o.sId,
										o.firstname,
										o.lastname,
										o.total,
										o.time,
										COUNT(*) AS tickets
							FROM		orders AS o,
										orders_tickets AS t
							WHERE		o.status >= ?
							AND			t.order = o.id
							GROUP BY	o.id
							ORDER BY	o.id DESC
							', array(OrderStatus::Approved));
	$_tpl->assign("ordersFinished", getOrdersFromInfo($result->fetchAll()));
	
	// charges
	$result = $_db->query('SELECT COUNT(*) as number FROM orders WHERE status = ?', array(OrderStatus::Approved));
	$charges = $result->fetch();
	$_tpl->assign("charges", $charges['number']);
	
	$result = $_db->query('	SELECT		c.id,
										COUNT(o.id) AS orders,
										SUM(o.total) AS total,
										c.date
							FROM		orders_charges AS c,
										orders AS o
							WHERE		o.charge = c.id
							GROUP BY	c.id
							ORDER BY	c.id DESC
							');
	$_tpl->assign("oldCharges", $result->fetchAll());
	
	$_tpl->display("members_tickets.tpl");
}
?>