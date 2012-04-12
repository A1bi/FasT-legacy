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
	Unser Sommernachtstheater 2012 <b>„Das Haus in Montevideo“</b> von Curt Goetz wird auf der Freilichtbühne im Historischen Ortskern von Kaisersesch an folgenden Tagen aufgeführt:
	<div class="container">
		<div class="title">
			<img src="/gfx/theater/montevideo/title.png" alt="Das Haus in Montevideo" />
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
	<div>
		<img src="/gfx/termine/kartenvorverkauf.png" alt="Kartenvorverkauf" />
	</div>
	<div style="margin-top: 10px;">
		<div style="font-size: 16px;">
			<p>Info-Hotline: <strong>(0 26 53) 28 27 09</strong> <em class="small" style="vertical-align: top;">(zum normalen Ortstarif)</em></p>
			<em>Der Vorverkauf wird in Kürze bekanntgegeben.</em>
		</div>
	</div>
	<div class="trenner"></div>
{include file="foot.tpl"}