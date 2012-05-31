<?php
include('./include/main.php');

requireSSL();
$_db = new database;
setlocale(LC_ALL, 'de_DE');

function getStringForDate($date) {
	return strftime("%A, den %d. %B um %H Uhr", $date);
}

if ($_GET['ajax']) {
	$response = array();
	
	$dates = array(1 => "1345222800", 2 => "1345309200", 3 => "1345381200", 4 => "1345827600", 5 => "1346432400", 6 => "1346518800");
	$prices = array("kids" => 6, "adults" => 12);
	
	switch ($_POST['action']) {
		
		case "getInfo":
			$response['status'] = "ok";
			
			$response['info'] = array(
				"dates" => array(),
				"prices" => $prices
			);
			
			foreach ($dates as $key => $date) {
				$response['info']['dates'][$key] = getStringForDate($date);
			}

			if (!is_array($_SESSION['order']) || $_SESSION['order']['lastUpdate']+600 < time()) {
				$_SESSION['order'] = array(
					"step" => 0,
					"lastUpdate" => time(),
					"date" => 0,
					"number" => array("kids" => 0, "adults" => 0),
					"address" => array("firstname" => "", "lastname" => "", "fon" => "", "email" => ""),
					"payment" => array("method" => "", "name" => "", "number" => "", "blz" => "", "bank" => "", "accepted" => false),
					"accepted" => false
				);
			}
			$response['order'] = $_SESSION['order'];
			
			break;
			
		case "placeOrder":
			$order = $_POST['order'];
			$ok = true;
			$error = "";
			
			if (is_array($_SESSION['order'])) {
				
				// check date
				if (empty($dates[$order['date']])) {
					$ok = false;
					$error = "date";
				}
				
				// check numbers and total
				$total = 0;
				foreach ($prices as $type => $price) {
					$total += $order['number'][$type]*$price;
				}
				if ($total == 0 || $total != $order['total']) {
					$ok = false;
					$error = "total";
				}
				
				// check address
				foreach ($_SESSION['order']['address'] as $key => $value) {
					if (empty($order['address'][$key]) || ($key == "email" && !preg_match("#^([a-zA-Z0-9_.-])+@(([a-zA-Z0-9-])+.)+([a-zA-Z]){2,9}$#", $order['address'][$key]))) {
						$ok = false;
						$error = "address";
					}
				}
				
				// check payment
				if ($order['payment']['method'] != "transfer") {
					foreach ($_SESSION['order']['payment'] as $key => $value) {
						if (empty($order['payment'][$key])) {
							$ok = false;
							$error = "payment";
						}
					}
				}
				
				// check if accepted TOS
				if (!$order['accepted']) {
					$ok = false;
					$error = "not accepted";
				}
				
			} else {
				$ok = false;
				$error = "unknown";
			}
			
			if ($ok) {
				$bsId = createId(6, "orders", "sId", true);
				
				// create order in db
				$_db->query('INSERT INTO orders VALUES (null, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0, 0, 0, "")',
						array($bsId, $order['address']['firstname'], $order['address']['lastname'], $order['address']['fon'], $order['address']['email'], $order['payment']['method'], $order['payment']['name'], $order['payment']['number'], $order['payment']['blz'], $order['payment']['bank'], ($order['payment']['accepted'] == "true") ? 1 : 0, ($order['accepted'] == "true") ? 1 : 0, $order['total'], time(), $_SERVER['REMOTE_ADDR']));
				$orderId = $_db->id();
				
				// create ticket in db
				$t = 0;
				foreach ($_SESSION['order']['number'] as $type => $val) {
					for ($i = 0; $i < $order['number'][$type]; $i++) {
						$sId = createId(6, "orders_tickets", "sId", true);
						$_db->query('INSERT INTO orders_tickets VALUES (null, ?, ?, ?, ?, 0, "", 0)', array($sId, $orderId, $order['date'], $t));
					}
					$t++;
				}
				
				// mail info to customer
				$tempOrder = $order;
				$tempOrder['date'] = getStringForDate($dates[$order['date']]);
				$_tpl->assign("order", $tempOrder);
				$_tpl->assign("prices", $prices);
				
				$body = $_tpl->fetch("order_mail_confirmation.tpl");
				$header = "From: " . mb_encode_mimeheader("Freilichtbühne am schiefen Turm", "UTF-8", "Q") . "<noreply@theater-kaisersesch.de>\n";
				$header .= "Reply-To:info@theater-kaisersesch.de\n";
				$header .= "Mime-Version: 1.0 Content-Type: text/plain; charset=utf-8 Content-Transfer-Encoding: quoted-printable";
				
				@mail($order['address']['email'], "Ihre Bestellung", $body, $header);
				
				if ($order['payment']['method'] == "charge") {
					$_tpl->assign("hash", md5($orderId));
					$body = $_tpl->fetch("order_mail_tickets.tpl");
					
					@mail($order['address']['email'], "Ihre Karten", $body, $header);
				}
				
				// create pdf document
				require('/usr/share/php/fpdf/fpdf.php');

				$pdf = new FPDF();
				$pdf->AddFont("Qlassik", "", "Qlassik_TB.php");
				$pdf->AddFont("Code39", "", "code39.php");
				$pdf->AddPage();
				$pdf->SetAutoPageBreak(false);

				$width = 120;
				$height = 60;
				$ticketHeight = $height+1;

				$tickets = 0;
				$result = $_db->query('SELECT * FROM orders_tickets WHERE `order` = ?', array($orderId));
				while ($ticket = $result->fetch()) {
					
					$orX = 15;
					$orY = 15 + $tickets * ($ticketHeight + 7);

					if ($orY + $ticketHeight > 280) {
						$pdf->AddPage();
						$tickets = 0;
						$orY = 15;
					}

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
					$pdf->Cell(50, 9, ($ticket['type'] == 1) ? "Erwachsener" : "Kind", 0, 2);
					$pdf->SetFont("Helvetica", "", "12");
					$pdf->Cell(50, 13, utf8_decode("Aufführung: ".getStringForDate($dates[$ticket['date']])), 0, 2);
					$pdf->SetFont("Helvetica", "", "18");
					$pdf->SetX($pdf->GetX() + 4);
					$pdf->Cell(50, 7, $prices[(($ticket['type'] == 1) ? "adults" : "kids")].",00 ".chr(128), 0, 2);

					$pdf->Line($orX + 2, $orY + 48, $orX + 42, $orY + 48);

					$pdf->setXY($orX + 2, $orY + 44);
					$pdf->SetFont("Helvetica", "", "9");
					$pdf->Cell(43, 16, "KN: ".$ticket['sId']." | BN: ".$bsId, 0, 0);

					$pdf->SetFont("Code39", "", "33");
					$pdf->Cell(50, 10, "*".$ticket['sId']."A".$bsId."*", 0, 2);

					$tickets++;
					
				}

				$pdf->SetX(15);
				$pdf->SetY($pdf->GetY() + 15);
				$pdf->SetFont("Helvetica", "", "18");
				$pdf->Cell(50, 10, "Hinweise:", 0, 2);
				$pdf->SetFont("Helvetica", "", "10");
				$pdf->MultiCell(0, 6, utf8_decode("Es handelt sich hierbei nur um vorläufige Karten. Sie erhalten gegen Vorlage dieser Ihre endgültigen Karten an der Abendkasse. Drucken Sie sich dazu bitte dieses Dokument aus. Gerne können Sie die Karten auch auseinanderschneiden und einzeln verwenden. Sollten Sie über keine Druckmöglichkeit verfügen, können Sie sich auch alternativ die KN-Nummer (unten links) jeder Karte notieren und an der Abendkasse vorzeigen."), 0, 2);

				$pdf->Output("./media/tickets/".md5($orderId).".pdf");
			}
			
			$response['status'] = ($ok) ? "ok" : "error";
			$response['error'] = $error;
			
			break;
	}
	
	echo json_encode($response);
	
} else {
	$_tpl->display("order.tpl");
}

?>
