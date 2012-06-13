{include file="members_head_board.tpl" title="Ticketbestellungen"}
<div class="hl section">Ticketbestellungen</div>

<div class="tickets">
	<div class="box stats">
		<div class="top">
			Übersicht über alle Kartenverkäufe
		</div>
		<div class="con">
			<table>
				<tr class="title">
					<td>Aufführung</td>
					<td>Ermäßigt</td>
					<td>Erwachsener</td>
					<td>Gesamt</td>
					<td>Umsatz</td>
				</tr>
				{foreach OrderManager::$theater['dates'] as $date}
				<tr>
					<td class="left">{OrderManager::getStringForDate($date)}</td>
					<td>{$stats['dates'][$date@key]['types'][0]|default:0}</td>
					<td>{$stats['dates'][$date@key]['types'][1]|default:0}</td>
					<td>{$stats['dates'][$date@key]['sum']|default:0}</td>
					<td>{$stats['dates'][$date@key]['revenue']|default:0} €</td>
				</tr>
				{/foreach}
				<tr class="total">
					<td class="left">Gesamt</td>
					<td>{$stats['total']['types'][0]|default:0}</td>
					<td>{$stats['total']['types'][1]|default:0}</td>
					<td>{$stats['total']['sum']|default:0}</td>
					<td>{$stats['total']['revenue']|default:0} €</td>
				</tr>
			</table>
		</div>
	</div>
	<div class="trenner"></div>
	{include file="members_tickets_table.tpl" title="Zu überprüfende Bestellungen" orders=$ordersCheck important=true}
	{include file="members_tickets_table.tpl" title="Unbezahlte Bestellungen" orders=$ordersPay important=true unpaid=true}
	<div class="box{if $charges} important{/if}">
		<div class="top">
			Ausstehende Lastschriften
		</div>
		<div class="con">
			{($charges > 0) ? $charges : "Keine"} Lastschrift{if $charges != 1}en{/if} ausstehend.{if $charges} <a href="?action=charge">Jetzt einreichen.</a>{/if}
		</div>
	</div>
	<div class="trenner"></div>
	{include file="members_tickets_table.tpl" title="Vergangene Bestellungen" orders=$ordersFinished}
	<div class="box stats">
		<div class="top">
			Vergangene Einreichungen von Lastschriften
		</div>
		<div class="con">
			{if $oldCharges|@count}
			<table>
				<tr class="title">
					<td>Datum</td>
					<td>Enthaltene Lastschriften</td>
					<td>Gesamtbetrag</td>
				</tr>
				{foreach $oldCharges as $charge}
				<tr>
					<td class="left">{$charge['date']|date_format:"%d.%m.%y, %H.%M Uhr"}</td>
					<td>{$charge['orders']}</td>
					<td>{$charge['total']} €</td>
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