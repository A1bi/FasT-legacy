{$theater = getData("theater_montevideo")}
{include file="head.tpl" title="Termine - Das Haus in Montevideo" cssfile="termine"}
{include file="termine_head.tpl"}
	Unser <b>Sommernachtstheater 2012 „<a href="/theater/montevideo" class="shadow">Das Haus in Montevideo</a>“</b> von Curt Goetz wird auf der Freilichtbühne im Historischen Ortskern von Kaisersesch an folgenden Tagen aufgeführt:
	<div class="container">
		<div class="title">
			<a href="/theater/montevideo"><img src="/gfx/theater/montevideo/title.png" alt="Das Haus in Montevideo" /></a>
		</div>
		<div class="box termine">
			<div class="top">
				Termine
			</div>
			<div class="con">
				<table>
					{foreach $theater['dates'] as $date}
					<tr>
						<td class="point"></td>
						<td class="day">{$date|date_format:"%A"},</td>
						<td class="date">{$date|date_format:"%d. %B"}</td>
						<td class="date">{$date|date_format:"%H"} Uhr</td>
					</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
	<p>Zum ersten Mal bietet die Freilichtbühne am schiefen Turm auch eine <b>Nachmittagsvorstellung am 19. August um 15 Uhr</b> für die ganze Familie an.<br />
	Alle anderen Aufführungen <b>beginnen um 19.00 Uhr</b>.
	<br /><b>Einlass</b> ist jeweils um <b>14</b> bzw. <b>18 Uhr</b>.</p>
	<p>Erleben Sie einen unvergesslichen Abend in Kaisersesch!</p>
	<div class="trenner"></div>
	<div class="hl">
		Kartenvorverkauf
		<div class="small">vom 4. Juni bis 15. August 2012</div>
	</div>
	<div class="prices">
		<table>
			<tr>
				<td class="price">6 €</td>
				<td>Jugendliche bis 16 Jahre</td>
			</tr>
			<tr>
				<td class="price">12 €</td>
				<td>Erwachsene</td>
			</tr>
		</table>
		<div>
			Ab einer Gruppengröße von 30 Personen gewähren wir einen Rabatt von 2 € auf die Erwachsenenkarten.
			<br />Kinder unter 3 Jahren sind frei, wenn sie noch keinen eigenen Sitzplatz benötigen.
		</div>
	</div>
	<a href="/tickets" class="hl online shadow">Karten jetzt online bestellen!</a>
	<div class="stores">
		<b>Oder auch an folgenden Vorverkaufsstellen:</b>
		<ul>
			<li>Sportstudio Otto Krechel, Kaisersesch</li>
			<li>Poststelle „Die Zwei“, Kaisersesch</li>
			<li>Buchhandlung Walgenbach, Kaisersesch</li>
			<li>Buchhandlung Layaa-Laulhé, Cochem</li>
			<li>röhrig forum, Treis-Karden</li>
		</ul>
	</div>
	<div class="trenner"></div>
	<div class="hcen hotline">
		Info-Hotline: <strong>(0 26 53) 28 27 09</strong> <em class="small">(zum Ortstarif)</em>
	</div>
{include file="foot.tpl"}