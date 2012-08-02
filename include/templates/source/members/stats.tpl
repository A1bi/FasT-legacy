{include file="members/head.tpl" title="Ticketstatistik"}
<div class="hl section">Ticketstatistik</div>

<div class="tickets">
	<div class="box stats">
		<div class="top">
			Übersicht über alle Kartenverkäufe
		</div>
		<div class="con">
			<table>
				<tr class="title">
					<td>Aufführung</td>
{foreach OrderManager::$theater['prices'] as $price}
					<td>{$price['desc']}</td>
{/foreach}
					<td>Gesamt</td>
					<td>Umsatz</td>
				</tr>
				{foreach OrderManager::$theater['dates'] as $date}
				<tr>
					<td class="left">{OrderManager::getStringForDate($date)}</td>
{foreach OrderManager::$theater['prices'] as $price}
					<td>{$stats['dates'][$date@key]['types'][$price@key]|default:0}</td>
{/foreach}
					<td>{$stats['dates'][$date@key]['sum']|default:0}</td>
					<td>{"%!.0n"|money_format:$stats['dates'][$date@key]['revenue']} €</td>
				</tr>
				{/foreach}
				<tr class="total">
					<td class="left">Gesamt</td>
{foreach OrderManager::$theater['prices'] as $price}
					<td>{$stats['total']['types'][$price@key]|default:0}</td>
{/foreach}
					<td>{$stats['total']['sum']|default:0}</td>
					<td>{"%!.0n"|money_format:$stats['total']['revenue']} €</td>
				</tr>
			</table>
		</div>
	</div>
</div>
{include file="foot.tpl"}