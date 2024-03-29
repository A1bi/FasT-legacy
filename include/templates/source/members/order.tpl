{$address=$order->getAddress()}
{$cat=$order->getCategory()}
{$payment=$order->getPayment()}
{$tickets=$order->getTickets()}
{$isFree=$order->getType() == OrderType::Free}
{$statuses[OrderStatus::Placed]="Buchung aufgenommen"}
{$statuses[OrderStatus::WaitingForApproval]="Warte auf Freischaltung.."}
{$statuses[OrderStatus::WaitingForPayment]="Warte auf Zahlung.."}
{$statuses[OrderStatus::WaitingForPaymentLater]="Warte auf Zahlung an der Abendkasse.."}
{$statuses[OrderStatus::Approved]="Überprüft, Lastschrift ausstehend.."}
{$statuses[OrderStatus::Finished]="<span class=\"finished\">Abgeschlossen</span>"}
{$statuses[OrderStatus::Cancelled]="<span class=\"cancelled\">Storniert</span>"}
{$events[OrderEvent::Placed]="Buchung aufgenommen"}
{$events[OrderEvent::Approved]="Buchung als geprüft markiert"}
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
{$payMethods[OrderPayMethod::Charge]="Lastschrift"}
{$payMethods[OrderPayMethod::Transfer]="Überweisung"}
{$payMethods[OrderPayMethod::CashUpFront]="Bar im Voraus"}
{$payMethods[OrderPayMethod::CashLater]="Bar an der Abendkasse"}
{include file="members/head.tpl" title="Buchungsdetails" jsfile="members/orders" cssfiles=["members/order"] pageBelongsTo="{if $isFree}free{else}orders{/if}"}

<div class="hl section">Buchungsdetails</div>

<div class="back"><a href="{if $isFree}freikarten{else}bestellungen{/if}">Zurück zur Übersicht</a></div>

<div class="topRow">
	<div class="box overview">
		<div class="top">
			Übersicht
		</div>
		<div class="con">
			<div class="type">{$types[$order->getType()]}</div>
			{if $smarty.get.edit}<form action="?action=edit" method="post">{/if}
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
				{if $address[$field[0]] == "0"}{$address[$field[0]]=""}{/if}
				<tr{if $field@first} class="newSection"{/if}>
					<td>{$field[1]}:</td>
					<td>
						{if $smarty.get.edit}
						<input type="text" name="address[{$field[0]}]" value="{$address[$field[0]]|escape}" />
						{else}
						{$address[$field[0]]|escape|default:"<em class=\"small\">nicht angegeben</em>"}
						{/if}
					</td>
				</tr>
				{/foreach}
				{if !$isFree}
				<tr class="newSection">
					<td>Zahlungsmethode:</td>
					<td>
						{if $smarty.get.edit}
						{html_options name="payment[method]" options=$payMethods selected=$payment['method']}
						{else}
						<b>{$payMethods[$payment['method']]}</b>
						{/if}
					</td>
				</tr>
				{if $payment['method'] == OrderPayMethod::Charge}
				{foreach [["name", "Kontoinhaber"], ["number", "Kontonummer"], ["blz", "BLZ"], ["bank", "Bankname"]] as $field}
				<tr>
					<td>{$field[1]}:</td>
					<td>
						{if $smarty.get.edit}
						<input type="text" name="payment[{$field[0]}]" value="{$payment[$field[0]]|escape}" />
						{else}
						{$payment[$field[0]]|escape}
						{/if}
					</td>
				</tr>
				{/foreach}
				{/if}
				{/if}
				<tr class="newSection">
					<td>Kategorie:</td>
					<td>
						{if $smarty.get.edit}
						{html_options name="category" options=OrderManager::getCategories(true) selected=$cat['id']}
						{else}
						{$cat['name']|escape|default:"<em class=\"small\">nicht zugeordnet</em>"}
						{/if}
					</td>
				</tr>
				<tr>
					<td>Notizen:</td>
					<td>
						{if $smarty.get.edit}
						<textarea name="notes">{$order->getNotes()|escape}</textarea>
						{else}
						{$order->getNotes()|escape|nl2br|default:"<em class=\"small\">nichts notiert</em>"}
						{/if}
					</td>
				</tr>
			</table>
			{if $smarty.get.edit}
			<div class="hcen"><input type="submit" name="edit" value="speichern" class="btn" /></div>
			</form>
			{/if}
		</div>
	</div>
	<div class="box actions">
		<div class="top">
			Aktionen
		</div>
		<div class="con">
			<ul>
				{if $isFree}
				<li><a href="?action=delete" class="delete">Reservierung löschen</a></li>
				{else}
				{if !$order->isCancelled()}
				{if $order->getStatus() == OrderStatus::WaitingForApproval}
				<li><a href="?action=approve">Für Lastschrift freischalten</a></li>
				{/if}
				{if $order->getStatus() == OrderStatus::WaitingForPayment || $order->getStatus() == OrderStatus::WaitingForPaymentLater}
				<li><a href="?action=markPaid" class="markPaid">Als bezahlt markieren</a></li>
				{if $address['email']}
				<li><a href="?action=sendPayReminder" class="sendPayReminder">Zahlungserinnerung senden</a></li>
				{/if}
				{/if}
				{if $order->getStatus() == OrderStatus::Approved}
				<li><a href="?action=approve&amp;undo=1">Freischaltung aufheben</a></li>
				{/if}
				<li><a href="#" id="cancelBtn">Stornieren</a></li>
				{/if}
				{/if}
				{if !$smarty.get.edit}
				<li><a href="?edit=1">Angaben bearbeiten</a></li>
				{/if}
			</ul>
		</div>
	</div>
	<div class="box actions cancel" id="cancelBox">
		<div class="top">
			Bestellung stornieren
		</div>
		<div class="con">
			<form action="?action=cancel" method="post">
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
				<td>Eingelöst</td>
			</tr>
			{foreach $tickets as $ticket}
			<tr class="{if $ticket->isCancelled()}cancelled{/if} {if $ticket->getId() == $smarty.get.ticket}highlighted{/if}">
				<td class="sId">{$ticket->getSid()}</td>
				<td>{$ticket->getDesc()}</td>
				<td>{$ticket->getDateString()}</td>
				<td>{if $ticket->getVoided()}{$ticket->getVoided()|date_format:"%H.%M Uhr"}{else}<em class="small">noch nicht</em>{/if}</td>
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
		<em>Für diese Buchung liegt bisher kein Eintrag im Protokoll vor.</em>
		{/if}
	</div>
</div>
{include file="foot.tpl"}