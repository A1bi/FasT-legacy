{$address=$order->getAddress()}
{$cat=$order->getCategory()}
{$payment=$order->getPayment()}
{$tickets=$order->getTickets()}
{$payMethods=[OrderPayMethod::Charge => "Lastschrift", OrderPayMethod::Transfer => "Überweisung"]}
{$isFree=$order->getType() == OrderType::Free}
{if $isFree}{$terminus="Reservierung"}{else}{$terminus="Bestellung"}{/if}
{$statuses[OrderStatus::Placed]="{$terminus} aufgenommen"}
{$statuses[OrderStatus::WaitingForApproval]="Warte auf Freischaltung.."}
{$statuses[OrderStatus::WaitingForPayment]="Warte auf Zahlung.."}
{$statuses[OrderStatus::Approved]="Überprüft, Lastschrift ausstehend.."}
{$statuses[OrderStatus::Finished]="<span class=\"finished\">Abgeschlossen</span>"}
{$statuses[OrderStatus::Cancelled]="<span class=\"cancelled\">Storniert</span>"}
{$events[OrderEvent::Placed]="{$terminus} aufgegeben"}
{$events[OrderEvent::Approved]="Bestellung als geprüft markiert"}
{$events[OrderEvent::Disapproved]="Überprüfung wieder aufgehoben"}
{$events[OrderEvent::MarkedAsPaid]="Als bezahlt markiert"}
{$events[OrderEvent::Charged]="Lastschrift eingereicht"}
{$events[OrderEvent::Cancelled]="Bestellung storniert"}
{$events[OrderEvent::CancelledTicket]="Einzelnes Ticket storniert"}
{$events[OrderEvent::SentTickets]="Tickets an Käufer gesendet"}
{$events[OrderEvent::SentPayReminder]="Zahlungserinnerung gesendet"}
{$types[OrderType::Online]="Online-Bestellung"}
{$types[OrderType::Manual]="Normale Bestellung (Telefon, etc.)"}
{$types[OrderType::Free]="Freikarten-Reservierung"}
{include file="members/head.tpl" title="{$terminus}sdetails" jsfile="members/orders"}

<div class="hl section">{$terminus}sdetails</div>

<div class="orderDetails">

<div class="back"><a href="bestellungen">Zurück zur Übersicht</a></div>

<div class="topRow">
	<div class="box overview">
		<div class="top">
			Übersicht
		</div>
		<div class="con">
			<div class="type">{$types[$order->getType()]}</div>
			<table>
				<tr>
					<td class="left">ON:</td>
					<td class="right"><b>{$order->getSId()}</b></td>
				</tr>
				<tr>
					<td>Tickets:</td>
					<td><b>{$order->getNumberOfValidTickets()}</b></td>
				</tr>
				{if !$isFree}
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
				{/if}
				{foreach [["firstname", "Vorname"], ["lastname", "Nachname"], ["affiliation", "Gruppe"], ["plz", "PLZ"], ["fon", "Telefon"], ["email", "e-mail"]] as $field}
				<tr{if $field@first} class="newSection"{/if}>
					<td>{$field[1]}:</td>
					<td>{$address[$field[0]]|escape|default:"<em class=\"small\">nicht angegeben</em>"}</td>
				</tr>
				{/foreach}
				{if !$isFree}
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
				{/if}
				<tr class="newSection">
					<td>Kategorie:</td>
					<td>{$cat['name']|escape|default:"<em class=\"small\">nicht zugeordnet</em>"}</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="box actions">
		<div class="top">
			Aktionen
		</div>
		<div class="con">
			<ul>
				{if $isFree}
				<li><a href="?id={$order->getId()}&amp;action=delete">Reservierung löschen</a></li>
				{else}
				{if !$order->isCancelled()}
				{if $order->getStatus() == OrderStatus::WaitingForApproval}
				<li><a href="?id={$order->getId()}&amp;action=approve">Für Lastschrift freischalten</a></li>
				{/if}
				{if $order->getStatus() == OrderStatus::WaitingForPayment}
				<li><a href="?order={$order->getId()}&amp;action=markPaid" class="markPaid">Als bezahlt markieren</a></li>
				{if $address['email']}
				<li><a href="?id={$order->getId()}&amp;action=sendPayReminder" class="sendPayReminder">Zahlungserinnerung senden</a></li>
				{/if}
				{/if}
				{if $order->getStatus() == OrderStatus::Approved}
				<li><a href="?id={$order->getId()}&amp;action=approve&amp;undo=1">Freischaltung aufheben</a></li>
				{/if}
				<li><a href="#" id="cancelBtn">Stornieren</a></li>
				{else}
				Keine Aktionen möglich, da Bestellung storniert.
				{/if}
				{/if}
				<li><a href="?id={$order->getId()}&amp;action=edit">Bearbeiten</a></li>
			</ul>
		</div>
	</div>
	<div class="box actions cancel" id="cancelBox">
		<div class="top">
			Bestellung stornieren
		</div>
		<div class="con">
			<form action="?id={$order->getId()}&amp;action=cancel" method="post">
			Grund:<br />
			<input type="text" name="reason" />
			<div class="hcen">
				<input type="submit" name="cancel" value="stornieren" />
			</div>
			</form>
		</div>
	</div>
</div>

<div class="box" id="tickets">
	<div class="top">
		Enthaltene Tickets
	</div>
	<div class="con">
		<table>
			<tr class="title">
				<td>TN</td>
				<td>Typ</td>
				<td>Aufführung</td>
			</tr>
			{foreach $tickets as $ticket}
			<tr class="{if $ticket->isCancelled()}cancelled{/if} {if $ticket->getId() == $smarty.get.ticket}highlighted{/if}">
				<td class="sId">{$ticket->getSid()}</td>
				<td>{$ticket->getDesc()}</td>
				<td>{$ticket->getDateString()}</td>
			</tr>
			{/foreach}
		</table>
	</div>
</div>

{$log=$order->getEvents()}
<div class="box">
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
				<td>{"{$event['firstname']} {$event['lastname']}"|escape}</td>
			</tr>
			{/foreach}
		</table>
		{else}
		<em>Für diese {$terminus} liegt bisher kein Eintrag im Protokoll vor.</em>
		{/if}
	</div>
</div>
</div>
{include file="foot.tpl"}