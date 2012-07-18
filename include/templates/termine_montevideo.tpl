{$theater = getData("theater_montevideo")}
{include file="head.tpl" title="Termine - Das Haus in Montevideo" cssfile="termine"}
{include file="termine_head.tpl"}
	Unser <b>Sommernachtstheater 2012 „<a href="/theater/montevideo">Das Haus in Montevideo</a>“</b> von Curt Goetz wird auf der Freilichtbühne im Historischen Ortskern von Kaisersesch an folgenden Tagen aufgeführt:
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
	<div>
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
				<tr>
					<td class="price">10 €</td>
					<td>Erwachsene in einer Gruppe ab 30 Personen</td>
				</tr>
				<tr>
					<td class="price">Frei</td>
					<td>Kleinkinder unter 3 Jahren, die keinen eigenen Sitzplatz benötigen</td>
				</tr>
			</table>
		</div>
		<a href="/tickets" class="hl online">Karten jetzt online bestellen!</a>
		<div style="font-size: 14px;">
			<b>Oder auch an folgenden Vorverkaufsstellen:</b>
			<div style="margin: 10px; margin-left: 20px;">
				<img src="/gfx/point.png" alt="" class="vcen" /> Sportstudio Otto Krechel, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Poststelle „Die Zwei“, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Buchhandlung Walgenbach, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Buchhandlung Layaa-Laulhé, Cochem<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> röhrig forum, Treis-Karden
			</div>
			<p class="hcen hotline">
				Info-Hotline: <strong>(0 26 53) 28 27 09</strong> <em class="small" style="vertical-align: top;">(zum Ortstarif)</em>
			</p>
		</div>
	</div>
{include file="foot.tpl"}