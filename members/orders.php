<?php
include('../include/members.php');

limitAccess(array(2));


function whereIn($column, $values) {
	for ($i = 0; $i < count($values); $i++) {
		$in .= "?";
		if ($i != count($values) - 1) {
			$in .= ",";
		}
		
	}
	
	return array(
		$column . " IN (" . $in . ")",
		$values
	);
}

function whereArray($column, $array) {
	global $wheres;
	
	if (is_array($array)) {
		$values = array();
		foreach ($array as $value) {
			if (intval($value)) {
				$values[] = $value;
			}
		}
		
		$wheres[] = whereIn($column, $values);
	}
}

if ($_GET['action'] == "search") {
	$wheres = array();
	$search = $_GET['search'];
	
	if (!empty($search['name'])) {
		$search['name'] = "%" . $search['name'] . "%";
		$wheres[] = array(
			"(o.firstname LIKE ? OR o.lastname LIKE ? OR o.affiliation LIKE ?)",
			array($search['name'], $search['name'], $search['name'])
		);
	}
	
	if (!empty($search['on'])) {
		$wheres[] = array(
			"o.sId = ?",
			array($search['on'])
		);
	}
	
	if (!empty($search['tn'])) {
		$wheres[] = array(
			"t.sId = ?",
			array($search['tn'])
		);
	}
	
	if (!empty($search['status'])) {
		$wheres[] = array(
			"o.status = ?",
			array($search['status'])
		);
	}
	
	whereArray("o.category", $search['categories']);
	whereArray("t.date", $search['dates']);
	whereArray("o.type", $search['types']);
	whereArray("o.payMethod", $search['payMethods']);
	
	if (count($search['voided']) == 1) {
		if ($search['voided'][0]) {
			$wheres[] = array(
				"t.voided = 0",
				array()
			);
		} else {
			$wheres[] = array(
				"t.voided > 0",
				array()
			);
		}
	}
	
	if (!empty($search['ticketNumber'])) {
		$comparator = ($search['comparator'] == 2) ? "=" : (($search['comparator'] == 1) ? "<" : ">");
		$having = "HAVING COUNT(t.id) " . $comparator . " " . intval($search['ticketNumber']);
	}
	
	
	$values = array();
	$i = 0;
	$number = count($wheres);
	foreach ($wheres as $expression) {
		$whereExpression .= $expression[0];
		foreach ($expression[1] as $value) {
			$values[] = $value;
		}
		$i++;
		if ($i != $number) {
			$whereExpression .= " AND ";
		}
	}
	
	
	if ($whereExpression) {
		$whereExpression .= " AND";
	}
	$result = $_db->query('SELECT o.id AS id FROM orders AS o, orders_tickets AS t WHERE ' . $whereExpression . ' o.id = t.`order` GROUP BY o.id ' . $having . ' ORDER BY id DESC', $values);
	while ($row = $result->fetch()) {
		$_tpl->assign("order", OrderManager::getOrderById($row['id']));
		$code .= $_tpl->fetch("members/orders_row.tpl");
		
	}
	
	$response = array(
		"ok" => true,
		"page" => 1,
		"pages" => 10,
		"results" => $code
	);
	
	echo json_encode($response);
	

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
			$orderType = ($_POST['free']) ? OrderType::Free : OrderType::Manual;
		
			$order = new Order();
			$order->create($orderType);
			if ($orderType == OrderType::Manual) {
				$order->setPayment($_POST['payment']);
			}
			$order->setAddress($_POST['address']);
			$order->setNotes($_POST['notes']);
				
			foreach (OrderManager::getTicketTypes($orderType) as $type => $price) {
				for ($i = 0; $i < $_POST['number'][$type]; $i++) {
					if (!$order->addTicket($type, $_POST['date'])) {
						break;
					}
				}
			}
			
			if ($orderType == OrderType::Manual && $order->getTotal() == 0) {
				$_tpl->assign("error", "Bitte wählen Sie mindestens eine Karte aus!");
			
			} else {
				$order->save();
				
				if ($orderType == OrderType::Manual) {
					if ($_POST['paid']) {
						$order->markPaid();
					}
					
					if ($_POST['payment']['method'] == OrderPayMethod::Transfer) {
						$redirect = "?finished=";
					} else {
						$redirect = "/mitglieder/buchungen/";
					}
				}
				
				redirectTo($redirect . $order->getId());
			}
		}
		
		$_tpl->display("members/orders_new.tpl");
	}
	

} elseif ($_GET['action'] == "showOpen") {
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
	
	$_tpl->display("members/orders_open.tpl");


} else {
	$_tpl->display("members/orders.tpl");
}
?>