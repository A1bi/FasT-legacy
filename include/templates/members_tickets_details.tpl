{include file="members_head_board.tpl" title="Ticketbestellungen - Bestellungsdetails" jsfile="members_tickets"}
{$address=$order->getAddress()}
{$payment=$order->getPayment()}
{$tickets=$order->getTickets()}
{$payMethods=[OrderPayMethod::Charge => "Lastschrift", OrderPayMethod::Transfer => "Überweisung"]}
{$statuses[OrderStatus::Placed]="Bestellung aufgenommen"}
{$statuses[OrderStatus::WaitingForApproval]="Warte auf Freischaltung.."}
{$statuses[OrderStatus::WaitingForPayment]="Warte auf Zahlung.."}
{$statuses[OrderStatus::Approved]="Überprüft, Lastschrift ausstehend.."}
{$statuses[OrderStatus::Finished]="<span class=\"finished\">Abgeschlossen</span>"}
{$statuses[OrderStatus::Cancelled]="<span class=\"cancelled\">Storniert</span>"}
{$events[OrderEvent::Placed]="Bestellung aufgegeben"}
{$events[OrderEvent::Approved]="Bestellung als geprüft markiert"}
{$events[OrderEvent::Disapproved]="Überprüfung wieder aufgehoben"}
{$events[OrderEvent::MarkedAsPaid]="Als bezahlt markiert"}
{$events[OrderEvent::Charged]="Lastschrift eingereicht"}
{$events[OrderEvent::Cancelled]="Bestellung storniert"}
{$events[OrderEvent::CancelledTicket]="Einzelnes Ticket storniert"}
{$events[OrderEvent::SentTickets]="Tickets an Käufer gesendet"}
{$events[OrderEvent::SentPayReminder]="Zahlungserinnerung gesendet"}
<div class="hl section">Bestellungsdetails</div>

<div class="orderDetails">

<div class="back"><a href="?">Zurück zur Übersicht</a></div>

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
					<td><b>{$order->getNumberOfValidTickets()}</b></td>
				</tr>
				<tr>
					<td>Gesamtbetrag:</td>
					<td><b>{$order->getTotal()} €</b></td>
				</tr>
				<tr>
					<td>Zeitpunkt:</td>
					<td>{$order->getTime()|date_format_x:"%@, %H.%M Uhr"}</td>
				</tr>
				<tr>
					<td>Status:</td>
					<td>
						{$statuses[$order->getStatus()]}
						<br />{if $order->getStatus() == OrderStatus::WaitingForPayment}{$difference={$order->getTime()|time_difference}} (seit {$difference} Tag{if $difference != 1}en{/if}){/if}
					</td>
				</tr>
				{if $order->isCancelled()}
				<tr>
					<td>Stornierungsgrund:</td>
					<td>{$order->getCancelReason()|escape}</td>
				</tr>
				{/if}
				<tr>
					<td>Zahlung erfolgt:</td>
					<td>{if $order->isPaid()}Ja{else}Nein{/if}</td>
				</tr>
				<tr class="newSection">
					<td>Käufer:</td>
					<td>{"{$address['firstname']} {$address['lastname']}"|escape}</td>
				</tr>
				<tr>
					<td>PLZ:</td>
					<td>{$address['plz']|default:"<em>unbekannt</em>"}</td>
				</tr>
				<tr>
					<td>Telefon:</td>
					<td>{$address['fon']|escape}</td>
				</tr>
				<tr>
					<td>e-mail:</td>
					<td>{$address['email']}</td>
				</tr>
				<tr class="newSection">
					<td>Zahlungsmethode:</td>
					<td><b>{$payMethods[$payment['method']]}</b></td>
				</tr>
				{if $payment['method'] == OrderPayMethod::Charge}
				<tr>
					<td>Kontoinhaber:</td>
					<td>{$payment['name']|escape}</td>
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
					<td>{$payment['bank']|escape}</td>
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
				{if $order->getStatus() == OrderStatus::WaitingForApproval}
				<li><a href="?order={$order->getId()}&amp;action=approve">Für Lastschrift freischalten</a></li>
				{/if}
				{if $order->getStatus() == OrderStatus::WaitingForPayment}
				<li><a href="?order={$order->getId()}&amp;action=markPaid" class="markPaid">Als bezahlt markieren</a></li>
				<li><a href="?order={$order->getId()}&amp;action=sendPayReminder" class="sendPayReminder">Zahlungserinnerung senden</a></li>
				{/if}
				{if $order->getStatus() == OrderStatus::Approved}
				<li><a href="?order={$order->getId()}&amp;action=approve&amp;undo=1">Freischaltung aufheben</a></li>
				{/if}
				<li><a href="#" id="cancelBtn">Stornieren</a></li>
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
			<form action="?order={$order->getId()}&amp;action=cancel" method="post">
			Grund:<br />
			<input type="text" name="reason" />
			<div class="hcen">
				<input type="submit" name="cancel" value="stornieren" />
			</div>
			</form>
		</div>
	</div>
</div>

<div class="box tickets">
	<div class="top">
		Tickets in dieser Bestellung
	</div>
	<div class="con">
		<table>
			<tr class="title">
				<td>Typ</td>
				<td>Aufführung</td>
			</tr>
			{foreach $tickets as $ticket}
			<tr{if $ticket->isCancelled()} class="cancelled"{/if}>
				<td>{$ticket->getDesc()}</td>
				<td>{$ticket->getDateString()}</td>
			</tr>
			{/foreach}
		</table>
	</div>
</div>

{$log=$order->getEvents()}
<div class="box tickets">
	<div class="top">
		Protokoll
	</div>
	<div class="con">
		{if $log|@count}
		<table>
			<tr class="title">
				<td>Zeitpunkt</td>
				<td>Ereignis</td>
				<td>ausgeführt von</td>
			</tr>
			{foreach $log as $event}
			<tr>
				<td>{$event['time']|date_format_x:"%@, %H.%M Uhr"}</td>
				<td>{$events[$event['event']]}</td>
				<td>{$event['realname']|escape}</td>
			</tr>
			{/foreach}
		</table>
		{else}
		<em>Für diese Bestellung liegt bisher kein Eintrag im Protokoll vor.</em>
		{/if}
	</div>
</div>
</div>
{include file="foot.tpl"}