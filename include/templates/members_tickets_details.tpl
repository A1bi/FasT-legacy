{include file="members_head_board.tpl" title="Ticketbestellungen - Bestellungsdetails" jsfile="members_tickets"}
{$address=$order->getAddress()}
{$payment=$order->getPayment()}
{$tickets=$order->getTickets()}
{$types=["Ermäßigt", "Erwachsener"]}
{$payMethods=["charge" => "Lastschrift", "transfer" => "Überweisung"]}
{$statuses=["Bestellung aufgenommen", "Warte auf Freischaltung..", "Warte auf Zahlung..", "Überprüft", "<span class=\"finished\">Abgeschlossen</span>", "<span class=\"cancelled\">Storniert</span>"]}
<div class="hl section">Bestellungsdetails</div>

<div class="orderDetails">

<div class="topRow">
	<div class="box overview">
		<div class="top">
			Übersicht
		</div>
		<div class="con">
			<table>
				<tr>
					<td class="left">ON:</td>
					<td class="right"><b>{$order->getSId()}</b></td>
				</tr>
				<tr>
					<td>Tickets:</td>
					<td><b>{$tickets|@count}</b></td>
				</tr>
				<tr>
					<td>Gesamtbetrag:</td>
					<td><b>{$order->getTotal()} €</b></td>
				</tr>
				<tr>
					<td>Status:</td>
					<td>{$statuses[$order->getStatus()]}</td>
				</tr>
				{if $order->isCancelled()}
				<tr>
					<td>Stornierungsgrund:</td>
					<td>{$order->getCancelReason()}</td>
				</tr>
				{/if}
				<tr>
					<td>Zahlung erfolgt:</td>
					<td>{if $order->isPaid()}Ja{else}Nein{/if}</td>
				</tr>
				<tr class="newSection">
					<td>Käufer:</td>
					<td>{$address['firstname']} {$address['lastname']}</td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td>{$address['fon']}</td>
				</tr>
				<tr>
					<td>e-mail:</td>
					<td>{$address['email']}</td>
				</tr>
				<tr class="newSection">
					<td>Zahlungsmethode:</td>
					<td><b>{$payMethods[$payment['method']]}</b></td>
				</tr>
				{if $payment['method'] == "charge"}
				<tr>
					<td>Kontoinhaber:</td>
					<td>{$payment['name']}</td>
				</tr>
				<tr>
					<td>Kontonummer:</td>
					<td>{$payment['number']}</td>
				</tr>
				<tr>
					<td>BLZ:</td>
					<td>{$payment['blz']}</td>
				</tr>
				<tr>
					<td>Bankname:</td>
					<td>{$payment['bank']}</td>
				</tr>
				{/if}
			</table>
		</div>
	</div>
	<div class="box actions">
		<div class="top">
			Aktionen
		</div>
		<div class="con">
			<ul>
				{if !$order->isCancelled()}
				{if $order->getStatus() == 1}<li><a href="?order={$order->getId()}&action=approve">freischalten</a></li>{/if}
				{if $order->getStatus() == 2}<li><a href="?order={$order->getId()}&action=markPaid">als bezahlt markieren</a></li>{/if}
				<li><a href="#" id="cancelBtn">stornieren</a></li>
				{else}
				Keine Aktionen möglich, da Bestellung storniert.
				{/if}
			</ul>
		</div>
	</div>
	<div class="box actions cancel" id="cancelBox">
		<div class="top">
			Bestellung stornieren
		</div>
		<div class="con">
			<form action="?order={$order->getId()}&action=cancel" method="post">
			Grund:<br />
			<input type="text" name="reason" />
			<div class="hcen">
				<input type="submit" name="cancel" value="stornieren" />
			</div>
			</form>
		</div>
	</div>
</div>

<div class="box">
	<div class="top">
		Tickets in dieser Bestellung
	</div>
	<div class="con">
		<table>
			<table>
			<tr class="title">
				<td>Typ</td>
				<td>Aufführung</td>
			</tr>
			{foreach $tickets as $ticket}
			<tr>
				<td>{$types[$ticket->getType()]}</td>
				<td>{$ticket->getDateString()}</td>
			</tr>
			{/foreach}
		</table>
		</table>
	</div>
</div>
</div>
{include file="foot.tpl"}