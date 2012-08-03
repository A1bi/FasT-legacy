{include file="members/head.tpl" title="Ticketbestellungen - Bestellung aufnehmen"}
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
					<td>Aufführung:</td>
					<td><select name="date">{foreach OrderManager::$theater['dates'] as $date}<option value="{$date@key}">{OrderManager::getStringForDate($date)}</option>{/foreach}</select></td>
					<td></td>
				</tr>
				{foreach OrderManager::$theater['prices'] as $price}
				<tr>
					<td>{$price['desc']}</td>
					<td><select name="number[{$price@key}]">{for $i=0 to 50}<option>{$i}</option>{/for}</select></td>
					<td>0 €</td>
				</tr>
				{/foreach}
				<tr>
					<td colspan="2"></td>
					<td>0 €</td>
				</tr>
			</table>
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
			</table>
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
					<td>
						<input type="text" name="address[email]" value="" class="field" />
					</td>
				</tr>
			</table>
			<div class="small">Keine dieser Angaben sind verpflichtend, Felder können also auch freigelassen werden.</div>
		</div>
	</div>
	
	<div class="box">
		<div class="top">
			Bezahlung
		</div>
		<div class="con">
			Per Überweisung. Angaben erscheinen nach Bestätigung der Bestellung.
		</div>
	</div>
	
	<div class="hcen">
		<input type="submit" name="confirm" value="Bestellung bestätigen" />
	</div>
</div>
</form>

{include file="foot.tpl"}