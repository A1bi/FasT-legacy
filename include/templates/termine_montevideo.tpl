{$days = ["Freitag", "Samstag", "Sonntag"]}
{$dates = [
	[0, "17. August"],
	[1, "18. August"],
	[2, "19. August"],
	[0, "24. August"],
	[0, "31. August"],
	[1, "01. September"]
]}
{include file="head.tpl" title="Termine - Das Haus in Montevideo" cssfile="termine"}
{include file="termine_head.tpl"}
	Unser Sommernachtstheater 2012 „<a href="/theater/montevideo">Das Haus in Montevideo</a>“ von Curt Goetz wird auf der Freilichtbühne im Historischen Ortskern von Kaisersesch an folgenden Tagen aufgeführt:
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
					{foreach $dates as $date}
					<tr>
						<td class="point"></td>
						<td class="day">{$days[$date[0]]},</td>
						<td class="date">{$date[1]} 2012</td>
					</tr>
					{/foreach}
				</table>
			</div>
		</div>
	</div>
	<p>Zum ersten Mal bietet die Freilichtbühne am schiefen Turm auch eine <b>Nachmittagsvorstellung</b> am 19. August um 15 Uhr <b>für die ganze Familie</b> an.<br />
	Alle anderen Aufführungen beginnen um <strong>19.00 Uhr</strong>.</p>
	<p>Erleben Sie einen unvergesslichen Abend in Kaisersesch!</p>
	<div class="trenner"></div>
	<div class="hl">
		Kartenvorverkauf
	</div>
	<div style="margin-top: 10px;">
		<div style="font-size: 18px;">
			<table>
				<tr>
					<td class="price"><strong>6 €</strong></td>
					<td>Jugendliche bis 16 Jahre</td>
				</tr>
				<tr>
					<td class="price"><strong>12 €</strong></td>
					<td>Erwachsene</td>
				</tr>
			</table>
		</div>
		<div style="font-size: 16px;">
			<p>Info-Hotline: <strong>(0 26 53) 28 27 09</strong> <em class="small" style="vertical-align: top;">(zum normalen Ortstarif)</em></p>
			<strong>Vorverkauf vom 4. Juni bis 15. August 2011 bei:</strong>
			<div style="margin: 10px; font-size: 14px;">
				<img src="/gfx/point.png" alt="" class="vcen" /> Sportstudio Otto Krechel, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Poststelle „Die Zwei“, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Buchhandlung Walgenbach, Kaisersesch<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> Buchhandlung Layaa-Laulhé, Cochem<br />
				<img src="/gfx/point.png" alt="" class="vcen" /> röhrig forum, Treis-Karden
			</div>
		</div>
	</div>
	<div class="trenner"></div>
{include file="foot.tpl"}