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
	
	{include file="members_tickets_table.tpl" title="Zu überprüfende Bestellungen" orders=$ordersCheck}
	
	{include file="members_tickets_table.tpl" title="Unbezahlte Bestellungen" orders=$ordersPay}
	
	{include file="members_tickets_table.tpl" title="Vergangene Bestellungen" orders=$ordersFinished}
</div>
{include file="foot.tpl"}