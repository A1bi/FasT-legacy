{include file="members/head.tpl" title="Freikarten - Reservierung" cssfiles=["members/orders"]}
<div class="hl section">Freikarten reservieren</div>

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
					<td>Aufführung:</td>
					<td><select name="date">{foreach OrderManager::getDates() as $date}<option value="{$date@key}">{OrderManager::getStringForDate($date)}</option>{/foreach}</select></td>
				</tr>
				<tr>
					<td>Anzahl Freikarten:</td>
					<td><select name="number">{for $i=0 to 15}<option>{$i}</option>{/for}</select></td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="box">
		<div class="top">
			Angaben zum Empfänger
		</div>
		<div class="con">
			<table>
				<tr>
					<td>Gruppe:</td>
					<td><input type="text" name="address[affiliation]" value="" class="field" /></td>
				</tr>
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
					<td>Kategorie:</td>
					<td>{html_options name="category" options=OrderManager::getCategories(true)}</td>
				</tr>
				<tr>
					<td>Notizen:</td>
					<td><textarea name="notes"></textarea></td>
				</tr>
			</table>
		</div>
	</div>
	
	<div class="hcen">
		<input type="submit" name="confirm" value="reservieren" />
	</div>
</div>
</form>

{include file="foot.tpl"}