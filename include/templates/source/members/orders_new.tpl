{$payMethods[OrderPayMethod::Transfer]="Überweisung"}
{$payMethods[OrderPayMethod::CashUpFront]="Bar im Voraus"}
{$payMethods[OrderPayMethod::CashLater]="Bar an der Abendkasse"}
{include file="members/head.tpl" title="Buchungen - Neue Buchung" jsfile="members/orders_new" cssfiles=["members/orders"]}
<div class="hl section">Bestellung aufnehmen</div>

<div class="back"><a href="?">Zurück zur Übersicht</a></div>

<div class="msg hcen">{$error}</div>
<form action="?action=new" method="post">
<div class="newOrder">
	<div class="box">
		<div class="top">
			Aufführung und Anzahl der Karten
		</div>
		<div class="con">
			<table>
				<tr>
					<td>Freikarten-Reservierung:</td>
					<td><label>Ja <input type="checkbox" name="free" /></label></td>
				</tr>
				<tr>
					<td>Aufführung:</td>
					<td><select name="date">{foreach OrderManager::getDates() as $date}<option value="{$date@key}">{OrderManager::getStringForDate($date)}</option>{/foreach}</select></td>
					<td></td>
				</tr>
			</table>
			<div class="nonFree">
				<table>
					{foreach OrderManager::getTicketTypes(OrderType::Manual) as $price}
					<tr class="type">
						<td>{$price['desc']}:</td>
						<td><select name="number[{$price@key}]">{for $i=0 to 50}<option>{$i}</option>{/for}</select></td>
						<td>je <span class="each">{$price['price']}</span> €</td>
					</tr>
					{/foreach}
					<tr>
						<td colspan="2"></td>
						<td><span class="total">0</span> €</td>
					</tr>
				</table>
			</div>
			<div class="free">
				<table>
					{foreach OrderManager::getTicketTypes(OrderType::Free) as $price}
					<tr class="type">
						<td>{$price['desc']}:</td>
						<td><select name="number[{$price@key}]">{for $i=0 to 50}<option>{$i}</option>{/for}</select></td>
					</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
	
	<div class="box">
		<div class="top">
			Angaben zum Käufer
		</div>
		<div class="con">
			<table>
				<tr>
					<td>Anrede:</td>
					<td><select name="address[gender]"><option value="1">Frau</option><option value="2">Herr</option></select></td>
				</tr>
				<tr>
					<td>Vorname:</td>
					<td><input type="text" name="address[firstname]" value="" class="field" /></td>
				</tr>
				<tr>
					<td>Nachname:</td>
					<td><input type="text" name="address[lastname]" value="" class="field" /></td>
				</tr>
				<tr>
					<td>Gruppe:</td>
					<td><input type="text" name="address[affiliation]" value="" class="field" /></td>
				</tr>
				<tr>
					<td>Kategorie:</td>
					<td>{html_options name="category" options=OrderManager::getCategories(true)}</td>
				</tr>
				<tr>
					<td>Notizen:</td>
					<td><textarea name="notes"></textarea></td>
				</tr>
			</table>
			<div class="nonFree">
				<div class="inner newSection"></div>
				<table>
					<tr>
						<td>PLZ:</td>
						<td><input type="text" name="address[plz]" value="" class="field" maxlength="5" /></td>
					</tr>
					<tr>
						<td>Telefon:</td>
						<td><input type="text" name="address[fon]" value="" class="field" /></td>
					</tr>
				</table>
				<div class="inner newSection"></div>
				<table>
					<tr>
						<td>e-mail-Adresse:</td>
						<td><input type="text" name="address[email]" value="" class="field" /></td>
					</tr>
				</table>
			</div>
			<div class="small">Keine dieser Angaben sind verpflichtend, Felder können also auch freigelassen werden.</div>
		</div>
	</div>
	
	<div class="box nonFree">
		<div class="top">
			Bezahlung
		</div>
		<div class="con">
			<table>
				<tr>
					<td>Zahlungsmethode:</td>
					<td>{html_options name="payment[method]" options=$payMethods} <label><input type="checkbox" name="paid" /> bereits bezahlt</label></td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="hcen">
		<input type="submit" name="confirm" value="Bestellung bestätigen" />
	</div>
</div>
</form>

{include file="foot.tpl"}