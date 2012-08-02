{include file="members/head.tpl" title="Ticketbestellungen" jsfile="members/orders"}
<div class="hl section">Ticketbestellungen</div>

<div class="tickets">
	<div class="box search">
		<div class="top">
			<form action="?action=search" method="post">
			Bestellung suchen anhand ON: <input type="tel" name="order" maxlength="6" /> oder TN: <input type="tel" name="ticket" maxlength="6" /> <input type="submit" name="search" value="suchen" />
			</form>
		</div>
	</div>
	{include file="members/orders_table.tpl" title="Zu überprüfende Bestellungen" aName="waitingForApproval" orders=$ordersCheck important=true}
	{include file="members/orders_table.tpl" title="Unbezahlte Bestellungen" aName="unpaid" orders=$ordersPay important=true unpaid=true}
	<div class="box charges{if $charges} important{/if}">
		<div class="top">
			Ausstehende Lastschriften
		</div>
		<div class="con">
			{($charges > 0) ? $charges : "Keine"} Lastschrift{if $charges != 1}en{/if} ausstehend.{if $charges} <a href="?action=charge">Jetzt einreichen.</a>{/if}
		</div>
	</div>
	<div class="trenner"></div>
	{include file="members/orders_table.tpl" title="Vergangene Bestellungen" aName="finished" orders=$ordersFinished}
	<div class="box">
		<div class="top">
			Vergangene Einreichungen von Lastschriften
		</div>
		<div class="con">
			{if $oldCharges|@count}
			<table>
				<tr class="title">
					<td>Zeitpunkt</td>
					<td>Enthaltene Lastschriften</td>
					<td>Gesamtbetrag</td>
					<td>Begleitzettel</td>
				</tr>
				{foreach $oldCharges as $charge}
				<tr>
					<td>{$charge['time']|date_format_x:"%@, %H.%M Uhr"}</td>
					<td>{$charge['orders']}</td>
					<td>{$charge['total']} €</td>
					<td><a href="?action=getChargesSheet&amp;id={$charge['id']}">download</a></td>
				</tr>
				{/foreach}
			</table>
			{else}
			Es wurden noch keine Lastschriften eingereicht.
			{/if}
		</div>
	</div>
</div>
{include file="foot.tpl"}