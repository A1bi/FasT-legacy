	<style type="text/css">
		.price {
			text-align: right;
			width: 45px;
			vertical-align: top;
		}
		.spons {
			position: absolute;
			top: 0px;
		}
	</style>
	<script type="text/javascript">
	//<![CDATA[

		var last = null;
		var space = 50;

		var sponsors = [];
		sponsors[0] = ["wfg", "WFG Kaisersesch"];
		sponsors[1] = ["lotto", "Lotto Stiftung Rheinland-Pfalz"];
		sponsors[2] = ["rwe", "RWE"];
		sponsors[3] = ["raiba", "Raiffeisenbank Kaisersesch-Kaifenheim eG"];
		sponsors[4] = ["fiat", "Auto-Gerhartz"];
		sponsors[5] = ["spmm", "Sparkasse Mittelmosel"];
		sponsors[6] = ["vbc", "Volksbank Cochem"];


		function move(logo) {
			last = logo;
			logo.animate({"left": -logo.width()}, (parseInt(logo.css("left"))+logo.width())*15, "linear", function () {
				$(this).css({"left": parseInt(last.css("left"))+last.width()+space});
				move(logo);
			});
		}

		$(document).ready(function () {
			$(sponsors).each(function () {
				$("#spons").append('<div class="spons"><img src="/images/termine/spons/'+this[0]+'.png" alt="'+this[1]+'" /></div>');
			});
		});

		$(window).load(function () {
			spons = $("#spons");
			left = 0;
			$(sponsors).each(function (i) {
				logo = spons.find(".spons").eq(i);
				logo.css({"left": left});
				left = left + logo.width() + space;
				move(logo);
			});
			spons.hide().css({"visibility": "visible"}).fadeIn(2500);
		});

	//]]>
	</script>
	<div class="hl">
		<img src="/images/termine/hl.jpg" alt="Termine" /><br />
	</div>
	Unser Sommernachtstheater 2010 &quot;<a href="/medicus">Der Medicus vom Orient</a>&quot; wird auf der Freilichtb&uuml;hne im Historischen Ortskern von Kaisersesch an folgenden Tagen aufgef&uuml;hrt:
	<div style="height: 220px; margin-top: 20px; margin-left: 10px; position: relative;">
		<div style="padding-top: 20px;">
			<a href="/medicus/"><img src="/images/medicus/small.png" alt="Der Medicus vom Orient" /></a><br />
		</div>
		<div class="box-small" style="position: absolute; right: 0px; top: 0px;">
			<div class="top">
				Termine
			</div>
			<div class="con">
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Freitag, 13. August 2010</strong><br />
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Samstag, 14. August 2010</strong><br />
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Sonntag, 15. August 2010</strong><br />
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Freitag, 20. August 2010</strong><br />
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Samstag, 21. August 2010</strong><br />
				<img src="/images/point.png" alt="" class="vcen" /> <strong>Sonntag, 22. August 2010</strong><br />
			</div>
		</div>
	</div>
	Wie in den letzten Jahren wird die bekannt gute Küche des Waldhotel Kurfürst und der Brasserie Alt Esch für Ihr leibliches Wohl sorgen.<br />
	Die Auff&uuml;hrung beginnt <strong>20.00 Uhr</strong>.<br />
	Erleben Sie einen unvergesslichen Abend in Kaisersesch!<br />
	<div style="margin-top: 10px;">
		<img src="/images/termine/kartenvorverkauf.jpg" alt="Kartenvorverkauf" />
	</div>
	<div style="margin-top: 10px;">
		<div style="font-size: 18px;">
			<table>
				<tr>
					<td class="price"><strong>10 &euro;</strong></td>
					<td>Erwachsene</td>
				</tr>
				<tr>
					<td class="price"><strong>5 &euro;</strong></td>
					<td>Schüler und Jugendliche bis 16 Jahre</td>
				</tr>
				<tr>
					<td class="price"><strong>26 &euro;</strong></td>
					<td>
						<span style="color: #0064ec;">Premiumkarte</span> inkl. reserviertem Sitzplatz und Teilnahme am Buffet
						<br /><em class="small">(nur gegen Voranmeldung)</em>
					</td>
				</tr>
			</table>
		</div>
		<div style="font-size: 16px;">
			<p>Info-Hotline: <strong>(0 26 53) 28 27 09</strong> <em class="small" style="vertical-align: top;">(zum normalen Ortstarif)</em></p>
			<strong>Vorverkauf vom 21. Juni bis 7. August 2010 bei:</strong>
			<div style="margin: 10px;">
				<img src="/images/point.png" alt="" class="vcen" /> Sportstudio Otto Krechel, Kaisersesch<br />
				<img src="/images/point.png" alt="" class="vcen" /> Poststelle &quot;Die Zwei&quot;, Kaisersesch<br />
				<img src="/images/point.png" alt="" class="vcen" /> Buchhandlung Walgenbach, Kaisersesch<br />
				<img src="/images/point.png" alt="" class="vcen" /> Buchhandlung Layaa - Laulhé, Cochem<br />
				<img src="/images/point.png" alt="" class="vcen" /> röhrig forum, Treis-Karden
			</div>
		</div>
	</div>
	<div style="margin-top: 20px;">
		<img src="/images/termine/sponsoren.jpg" alt="Unsere Sponsoren" />
	</div>
	<div style="position: relative; width: 640px; height: 150px; overflow: hidden;">
		<div style="position: absolute; top: 20px; left: 0px; white-space: nowrap; visibility: hidden; display: block;" id="spons">
			&nbsp;
		</div>
		<div style="position: absolute; top: 0px; left: 0px;">
			<img src="/images/termine/sponsoren-fg.png" alt="" />
		</div>
	</div>