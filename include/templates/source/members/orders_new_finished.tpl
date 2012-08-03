{include file="members/head.tpl" title="Ticketbestellungen - Bestellung aufnehmen"}
<div class="hl section">Bestellung aufnehmen</div>

<div class="back"><a href="?">Zurück zur Übersicht</a></div>

<div class="box">
	<div class="top">
		Bestellung aufgenommen!
	</div>
	<div class="con">
		Die Bestellung wurde erfolgreich aufgenommen!<br />
		Sollte der Käufer mit Überweisung zahlen, geben Sie ihm bitte folgende Daten an:
		<table class="inner">
			<tr>
				<td>Kontoinhaber:</td>
				<td>Freilichtbühne am schiefen Turm e.V.</td>
			</tr>
			<tr>
				<td>Kontonummer:</td>
				<td>178167</td>
			</tr>
			<tr>
				<td>BLZ:</td>
				<td>57069144</td>
			</tr>
			<tr>
				<td>Bankname:</td>
				<td>Raiffeisenbank Kaisersesch</td>
			</tr>
			<tr>
				<td>Verwendungszweck:</td>
				<td>ON{$order->getSId()}</td>
			</tr>
			<tr>
				<td>Betrag:</td>
				<td>{$order->getTotal()} €</td>
			</tr>
		</table>
		<div class="hcen">
			<a href="?action=showDetails&amp;order={$order->getId()}">Zu den Details dieser Bestellung</a>
		</div>
	</div>
</div>

{include file="foot.tpl"}