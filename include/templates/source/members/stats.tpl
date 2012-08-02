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
{$stat=$stats->getValue($date@key, $price@key, 0)}
					<td>{$stat['number']}</td>
{/foreach}
{$stat=$stats->getValue($date@key, -1, 0)}
					<td>{$stat['number']}</td>
					<td>{"%!.0n"|money_format:$stat['revenue']} €</td>
				</tr>
				{/foreach}
				<tr class="total">
					<td class="left">Gesamt</td>
{foreach OrderManager::$theater['prices'] as $price}
{$stat=$stats->getValue(-1, $price@key, 0)}
					<td>{$stat['number']}</td>
{/foreach}
{$stat=$stats->getValue(-1, -1, 0)}
					<td>{$stat['number']}</td>
					<td>{"%!.0n"|money_format:$stat['revenue']} €</td>
				</tr>
			</table>
		</div>
	</div>
</div>
{include file="foot.tpl"}