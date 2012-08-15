{include file="head.tpl" title="Karten bestellen" cssfile="order" jsfile="order" noSlides=true}
<div class="secure">
	<span><img src="/gfx/order/secure.png" alt="" /> Ihre Bestellung wird über eine gesicherte Verbindung ausgeführt.</span>
</div>	
<div class="hl">
	Karten bestellen
</div>
<div class="progress">
	<div class="step bar"></div>
	<div class="step current">Termin</div>
	<div class="step">Adresse</div>
	<div class="step">Bezahlen</div>
	<div class="step">Bestätigen</div>
</div>
<div class="stepBox">
	<div class="stepCon date">
		Bitte wählen Sie Ihre gewünschte Aufführung:
		<ul class="inner"></ul>
		<div class="soldOut small">
			Für ausverkaufte Aufführungen sind lediglich an der Abendkasse noch wenige Restkarten erhältlich.
		</div>
		<div class="number">
			Bitte wählen Sie die gewünschte Anzahl an Karten:
			<table class="inner rTable">
				<tr class="kids">
					<td class="number"><select name="kids">{for $i=0 to 15}<option>{$i}</option>{/for}</select></td>
					<td class="type">Ermäßigt<div class="small">Jugendliche bis 16 Jahre</div></td>
					<td class="single">je <span>5</span> €</td>
					<td class="total"><span>0</span> €</td>
				</tr>
				<tr class="adults">
					<td class="number"><select name="adults">{for $i=0 to 15}<option>{$i}</option>{/for}</select></td>
					<td class="type">Erwachsene</td>
					<td class="single">je <span>10</span> €</td>
					<td class="total"><span>0</span> €</td>
				</tr>
				<tr class="total">
					<td colspan="3">Gesamt</td>
					<td class="total"><span>0</span> €</td>
				</tr>
			</table>
			<div class="inner rTable blocker tooMany">
				Leider sind für die gewünschte Aufführung nur noch <span>3</span> Karten verfügbar.
			</div>
		</div>
	</div>
	<div class="stepCon address">
		Bitte füllen Sie die folgenden Felder entsprechend aus:
		<table class="inner">
			<tr>
				<td>Anrede:</td>
				<td><select name="gender"><option value="1">Frau</option><option value="2">Herr</option></select></td>
			</tr>
			<tr>
				<td>Vorname:</td>
				<td><input type="text" name="firstname" value="" class="field" /></td>
			</tr>
			<tr>
				<td>Nachname:</td>
				<td><input type="text" name="lastname" value="" class="field" /></td>
			</tr>
			<tr>
				<td>Gruppe:</td>
				<td>
					<input type="text" name="affiliation" value="" class="field" />
					<div class="small">Falls Sie Karten für eine Gruppe bestellen, können Sie diese hier angeben. Ansonsten lassen Sie dieses Feld einfach frei.</div>
				</td>
			</tr>
		</table>
		<div class="inner newSection"></div>
		<table class="inner">
			<tr>
				<td>PLZ:</td>
				<td><input type="text" name="plz" value="" class="field" maxlength="5" /></td>
			</tr>
			<tr>
				<td>Telefon:</td>
				<td><input type="text" name="fon" value="" class="field" /></td>
			</tr>
		</table>
		<div class="inner newSection"></div>
		<table class="inner">
			<tr>
				<td>e-mail-Adresse:</td>
				<td>
					<input type="text" name="email" value="" class="field" />
					<div class="small">Bitte achten Sie auf die Richtigkeit Ihrer e-mail-Adresse, da an sie später Ihre Karten geschickt werden!</div>
				</td>
			</tr>
		</table>
		<div class="small">
			Diese Daten sind nur für eventuelle Rückfragen erforderlich und werden nach der Aufführung vollständig aus unserem System gelöscht.
		</div>
	</div>
	<div class="stepCon payment">
		Bitte wählen Sie eine der folgenden Zahlungsmethoden aus:
		<table class="inner">
			<tr>
				<td class="radio"><input type="radio" name="method" value="charge" /></td>
				<td>
					<b>Lastschrift</b>
					<div class="small">Schnell und bequem, Sie erhalten Ihre Karten sofort nach der Bestellung!</div>
				</td>
			</tr>
			<tr>
				<td class="radio"><input type="radio" name="method" value="transfer" /></td>
				<td>
					<b>Vorkasse per Überweisung</b>
					<div class="small">Sie erhalten Ihre Karten nach Zahlungseingang.</div>
				</td>
			</tr>
		</table>
		<div class="inner rTable blocker transferDisabled">
			Überweisungen akzeptieren wir nur bis spätestens <b>drei Tage vor der Aufführung</b>. Da dies bei Ihrer gewünschten Aufführung nicht mehr möglich ist, bitten wir Sie, stattdessen das Lastschriftverfahren zu wählen.
		</div>
		<div class="charge">
			<div>
				Bitte geben für das Lastschriftverfahren Ihre Bankdaten an:
			</div>
			<table class="inner">
				<tr>
					<td>Kontoinhaber:</td>
					<td style="width: 70%;"><input type="text" name="name" value="" class="field" /></td>
				</tr>
				<tr>
					<td>Kontonummer:</td>
					<td><input type="text" name="number" value="" class="field" maxlength="10" /></td>
				</tr>
				<tr>
					<td>BLZ:</td>
					<td><input type="text" name="blz" value="" class="field" maxlength="8" /></td>
				</tr>
				<tr>
					<td>Name der Bank:</td>
					<td><input type="text" name="bank" value="" class="field" /></td>
				</tr>
			</table>
			<div class="small">
				Ihre Bankdaten werden nach erfolgter Zahlung aus unserem System vollständig gelöscht.
			</div>
			<div class="accept">
				<input type="checkbox" name="accepted" /> Ich akzeptiere, dass mein Konto mit dem zu zahlenden Betrag belastet wird.
			</div>
		</div>
	</div>
	<div class="stepCon confirm">
		Bitte kontrollieren Sie noch einmal Ihre Bestellung und klicken anschließend auf "bestätigen".
		<table class="inner rTable">
			<tr>
				<td>Name:</td>
				<td><span class="firstname"></span> <span class="lastname"></span></td>
			</tr>
			<tr>
				<td>Telefonnummer:</td>
				<td class="fon"></td>
			</tr>
			<tr>
				<td>e-mail-Adresse:</td>
				<td class="email"></td>
			</tr>
		</table>
		<div class="inner">
			Karten für <span class="date"></span>:
			<table class="rTable">
				<tr class="kids">
					<td><span class="number"></span> Ermäßigt</td>
					<td class="single">je <span></span> €</td>
					<td class="total"><span></span> €</td>
				</tr>
				<tr class="adults">
					<td><span class="number"></span> Erwachsene</td>
					<td class="single">je <span></span> €</td>
					<td class="total"><span></span> €</td>
				</tr>
				<tr class="total">
					<td colspan="2">Gesamt</td>
					<td class="total"><span></span> €</td>
				</tr>
			</table>
		</div>
		<div class="payment inner">
			<div class="transfer">
				Bezahlung per Überweisung. Unsere Kontodaten werden Ihnen nach der Bestellung angezeigt.
			</div>
			<div class="charge">
				Bezahlung per Lastschrift von folgendem Konto:
				<table class="rTable">
					<tr>
						<td>Kontoinhaber:</td>
						<td class="name"></td>
					</tr>
					<tr>
						<td>Kontonummer:</td>
						<td class="number"></td>
					</tr>
					<tr>
						<td>BLZ:</td>
						<td class="blz"></td>
					</tr>
					<tr>
						<td>Name der Bank:</td>
						<td class="bank"></td>
					</tr>
				</table>
			</div>
		</div>
		<div class="accept">
			<input type="checkbox" name="accept" /> Ich akzeptiere die <a href="/agb" target="_blank">AGB</a> der Freilichtbühne am schiefen Turm e.V.
		</div>
	</div>
	<div class="stepCon finish">
		<div class="hl">
			Vielen Dank für Ihre Bestellung!
		</div>
		<div class="payment">
			<div class="transfer inner">
				Bitte überweisen Sie den Betrag von <b><span class="total">10</span> €</b> auf folgendes Konto:
				<table class="rTable">
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
						<td>ON<span class="sId"></span></td>
					</tr>
				</table>
			</div>
			<div class="charge">
				Ihre Karten werden in Kürze an Ihre e-mail Adresse versandt.
			</div>
		</div>
		<div>
			Alle Informationen rund um Ihre Bestellung erhalten Sie ebenfalls noch einmal per e-mail.
		</div>
	</div>
</div>
<div class="btns">
	<div class="btn prev disabled">zurück</div>
	<div class="msg"></div>
	<div class="btn next disabled">
		<span class="action">weiter</span>
		<span class="spinner"><img src="/gfx/order/loading.gif" alt="Bitte warten" /></span>
	</div>
</div>
{include file="foot.tpl"}