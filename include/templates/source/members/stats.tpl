{include file="members/head.tpl" title="Ticketstatistik" cssfiles=["members/stats"] jsfile="members/stats"}
<div class="hl section">Ticketstatistik</div>

<div class="box stats">
	<div class="top">
		Übersicht über alle Kartenverkäufe
	</div>
	<div class="con">
		Anzeigen: {html_options name="orderType" options=$orderTypes['options'] selected=$orderTypes['selected']}
		<table>
			<tr class="title">
				<td>Aufführung</td>
{foreach OrderManager::getTicketTypes(OrderType::Manual) as $price}
				<td>{$price['desc']}</td>
{/foreach}
				<td>Gesamt</td>
				<td>Umsatz</td>
			</tr>
			{foreach OrderManager::getDates() as $date}
			<tr>
				<td class="left" title="{OrderManager::getStringForDate($date)}">{$date|date_format:"%d. %B, %H Uhr"}</td>
{foreach OrderManager::getTicketTypes(OrderType::Manual) as $price}
				<td class="type"></td>
{/foreach}
				<td class="total"></td>
				<td><span class="revenue"></span> €</td>
			</tr>
			{/foreach}
			<tr class="total">
				<td class="left">Gesamt</td>
{foreach OrderManager::getTicketTypes(OrderType::Manual) as $price}
				<td class="type"></td>
{/foreach}
				<td class="total"></td>
				<td><span class="revenue"></span> €</td>
			</tr>
		</table>
		<div class="edit"><a href="#">bearbeiten</a></div>
	</div>
</div>
{include file="foot.tpl"}