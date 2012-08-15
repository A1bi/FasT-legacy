<?php
include('../include/members.php');

limitAccess(array(2));

function getOrdersByStatus($status, $orderBy = "sId ASC", $operator = "=") {
	global $_db;
	
	$maxEntries = 10;
	
	// construct query
	$cond = ' FROM orders WHERE	type IN (?, ?) AND status '.$operator.' ?';
	$order = ' ORDER BY	'.$orderBy;
	$args = array(OrderType::Online, OrderType::Manual, $status);
	if (!$_GET['showAll']) {
		$limit .= ' LIMIT 0, '.$maxEntries;
	
		// get number of entries
		$query = 'SELECT COUNT(*) AS number' . $cond;
		$result = $_db->query($query, $args);
		$count = $result->fetch();
	}

	// get entries
	$query = 'SELECT id' . $cond . $order . $limit;
	$result = $_db->query($query, $args);
	
	$orders = array();
	while ($id = $result->fetch()) {
		$orders[] = OrderManager::getOrderById($id['id']);
	}
	
	return array(
		"more" => count($orders) < $count['number'],
		"orders" => $orders
	);
}

if (!empty($_GET['order'])) {
	$order = OrderManager::getInstance()->getOrderById($_GET['order']);
	// not found ?
	if (!$order) {
		redirectTo("?");
	}
	
	if ($_GET['action'] == "showDetails") {
		$_tpl->assign("order", $order);
		$_tpl->display("members/orders_details.tpl");
			
	} else {
		loadComponent("queue");
		$queue = new Queue();
		
		switch ($_GET['action']) {
			case "markPaid":
				if ($order->markPaid()) {
					if ($order->getType() != OrderType::Online) break;
					
					$payment = $order->getPayment();
					if ($payment['method'] == OrderPayMethod::Transfer) {
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
				
			case "sendPayReminder":
				$order->mailPayReminder();
				break;
		}
		
		redirectTo("?" . (($_GET['goto'] != "overview") ? "action=showDetails&order=".$order->getId() : ""));
	}

} elseif ($_GET['action'] == "search") {
	if ($_POST['order']) {
		$result = $_db->query('SELECT id FROM orders WHERE sId = ?', array($_POST['order']));
	
	} else {
		$result = $_db->query('SELECT o.id, t.id AS tId FROM orders AS o, orders_tickets AS t WHERE t.sId = ? AND o.id = t.order', array($_POST['ticket']));
		
	}
	
	$order = $result->fetch();
	if ($order['id']) {
		redirectTo("?action=showDetails&order=" . $order['id'] . (($_POST['ticket']) ? "&ticket=" . $order['tId'] . "#tickets" : ""));
	}
	
	redirectTo("?");

} elseif ($_GET['action'] == "getChargesSheet") {
	$file = "./media/charges/" . $_GET['id'] . ".pdf";
	if (file_exists($file)) {
		header('Content-type: application/pdf');
		readfile($file);
	} else {
		redirectTo("?");
	}

} elseif ($_GET['action'] == "charge") {
	
	$result = $_db->query('SELECT id FROM orders WHERE status = ?', array(OrderStatus::Approved));
	$charges = $result->fetchAll();
	$nCharges = count($charges);
	
	if ($nCharges) {
		$chargeInfo = getData("charge");
	
		$_db->query('INSERT INTO orders_charges VALUES()');
		$chargeId = $_db->id();
		
		loadComponent("dtaus");
		$dta = new DTAUS("LK", $chargeInfo['sender'], $chargeId);
		
		foreach ($charges as $charge) {
			$order = OrderManager::getOrderById($charge['id']);
			$payment = $order->getPayment();
			$paymentDetails = array("name" => $payment['name'], "account" => $payment['number'], "blz" => $payment['blz'], "bank" => $payment['bank']);
	
			$references = $chargeInfo['references'];
			$references[] = "ON".$order->getSId();
			$transaction = new Transaction("charge", $order->getTotal(), $paymentDetails, $references);
			$dta->addTransaction($transaction);
			
			$order->charge($chargeId);
		}
		
		// send mail to bank containing the dta file
		require("/usr/share/php/libphp-phpmailer/class.phpmailer.php");
		
		$company = OrderManager::getCompanyInfo();
		
		$dta->generateData();
		
		$mail = new PHPMailer();
		$mail->CharSet = 'utf-8';
		
		$mail->SetFrom($company['email'], $company['name']);
		// don't send anything to the bank in dev mode
		$mail->AddAddress(($_config['dev']) ? "a1bi@me.com" : $chargeInfo['bankEmail']);
		
		$mail->Subject = "DTA-Datei";
		$mail->Body = $_tpl->fetch("mail/tickets_bank.tpl");
		
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
		$pdf->SetAuthor($company['name'], true);
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
	}
	
	redirectTo("?");


} elseif ($_GET['action'] == "new") {
	if ($_GET['finished']) {
		$order = OrderManager::getOrderById($_GET['finished']);
		
		$_tpl->assign("order", $order);
		$_tpl->display("members/orders_new_finished.tpl");
		
	} else {
		if ($_POST['confirm']) {
		
			$order = new Order();
			$order->create(OrderType::Manual);
			$order->setPayment($_POST['payment']);
			$order->setAddress($_POST['address']);
				
			foreach (OrderManager::getTicketTypes(OrderType::Manual) as $type => $price) {
				for ($i = 0; $i < $_POST['number'][$type]; $i++) {
					if (!$order->addTicket($type, $_POST['date'])) {
						break;
					}
				}
			}
			
			if ($order->getTotal() == 0) {
				$_tpl->assign("error", "Bitte wählen Sie mindestens eine Karte aus!");
			
			} else {
				$order->save();
				
				if ($_POST['paid']) {
					$order->markPaid();
				}
				
				redirectTo("?action=new&finished=" . $order->getId());
			}
		}
		
		$_tpl->display("members/orders_new.tpl");
	}


// nothing chosen -> show overview
} else {
	// orders to check
	$_tpl->assign("ordersCheck", getOrdersByStatus(OrderStatus::WaitingForApproval));
	
	
	// orders to pay
	$_tpl->assign("ordersPay", getOrdersByStatus(OrderStatus::WaitingForPayment));
	
	
	// finished or closed orders
	$_tpl->assign("ordersFinished", getOrdersByStatus(OrderStatus::Approved, "id DESC", ">="));
	
	// charges
	$result = $_db->query('SELECT COUNT(*) AS number, SUM(total) AS total FROM orders WHERE status = ?', array(OrderStatus::Approved));
	$_tpl->assign("charges", $result->fetch());
	
	$result = $_db->query('	SELECT		c.id,
										COUNT(o.id) AS orders,
										SUM(o.total) AS total,
										UNIX_TIMESTAMP(c.time) AS time
							FROM		orders_charges AS c,
										orders AS o
							WHERE		o.charge = c.id
							GROUP BY	c.id
							ORDER BY	c.id DESC
							');
	$_tpl->assign("oldCharges", $result->fetchAll());
	
	$_tpl->display("members/orders.tpl");
}
?>