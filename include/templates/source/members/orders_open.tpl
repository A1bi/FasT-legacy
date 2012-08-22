{include file="members/head.tpl" title="Ticketbestellungen" cssfiles=["members/orders"] jsfile="members/orders" head="members/orders_open_head.tpl" pageBelongsTo="ordersOpen"}
<div class="hl section">
	Offene Buchungen
</div>
<div class="orders">
	<div class="box new">
		<div class="top">
			<a href="/mitglieder/buchungen/neu">Neue Buchung</a>
		</div>
	</div>
	{include file="members/orders_results.tpl" title="Zu überprüfende Bestellungen"}
	{include file="members/orders_results.tpl" title="Unbezahlte Bestellungen"}
	<div class="box charges{if $charges['number']} important{/if}">
		<div class="top">
			Ausstehende Lastschriften
		</div>
		<div class="con">
			{if $charges['number'] < 1}<em class="small">Keine{else}{$charges['number']}{/if} Lastschrift{if $charges['number'] != 1}en{/if}{if $charges['number']} ({$charges['total']} €){/if} ausstehend.{if $charges['number']} <a href="?action=charge">Jetzt einreichen.</a>{else}</em>{/if}
		</div>
	</div>
	<div class="trenner"></div>
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
					<td><a href="?action=getChargesSheet&amp;id={$charge['id']}" target="_blank">download</a></td>
				</tr>
				{/foreach}
			</table>
			{else}
			<em class="small">Es wurden noch keine Lastschriften eingereicht.</em>
			{/if}
		</div>
	</div>
</div>
{include file="foot.tpl"}