{include file="members/head.tpl" title="Alle Buchungen" cssfiles=["members/orders"] jsfile="members/orders" head="members/orders_head.tpl" pageBelongsTo="ordersAll"}
<div class="hl section">
	Alle Buchungen
</div>
<div class="orders">
	<div class="box new">
		<div class="top">
			<a href="/mitglieder/buchungen/neu">Neue Buchung</a>
		</div>
	</div>
	<div class="box search">
		<div class="top">Suche</div>
		<div class="con">
			<table>
				<tr>
					<td>Name oder Gruppe:</td>
					<td><input type="text" name="name" /></td>
				</tr>
				<tr>
					<td>Kategorie:</td>
					<td>{html_checkboxes name="categories" options=OrderManager::getCategories() separator="<br />"}</td>
				</tr>
			</table>
			<div class="showMore">weitere Suchkriterien</div>
			<div class="more">
				<table>
					<tr>
						<td>ON:</td>
						<td><input type="tel" name="on" class="sId" /></td>
					</tr>
					<tr>
						<td>TN:</td>
						<td><input type="tel" name="tn" class="sId" /></td>
					</tr>
					<tr>
						<td>Datum:</td>
						<td>
							{$dates=[]}
							{foreach OrderManager::getDates() as $date}
							{$dates[$date@key] = OrderManager::getStringForDate($date)}
							{/foreach}
							{html_checkboxes name="dates" options=$dates separator="<br />"}
						</td>
					</tr>
					<tr>
						<td>Buchungstyp:</td>
						<td>{html_checkboxes name="types" options=["Online", "Normal", "Freikarten"]}</td>
					</tr>
					<tr>
						<td>Zahlungsmethode:</td>
						<td>{html_checkboxes name="payMethods" output=["Lastschrift", "Überweisung", "Bar im Voraus", "Bar an der Abendkasse"] values=[1, 2, 3, 4] separator="<br />"}</td>
					</tr>
					<tr>
						<td>Karten eingelöst:</td>
						<td>{html_checkboxes name="voided" options=["ja", "nein"]}</td>
					</tr>
					<tr>
						<td>Anzahl Karten:</td>
						<td>{html_options name="comparator" output=["größer", "kleiner", "gleich"] values=[0, 1, 2]} <input type="tel" name="ticketNumber" value="0" /></td>
					</tr>
				</table>
			</div>
		</div>
	</div>
	{include file="members/orders_results.tpl" title="Alle Buchungen"}
</div>
{include file="foot.tpl"}