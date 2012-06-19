{include file="head.tpl" title="Karten bestellen" cssfile="order"}
<div class="hl">
	Karten online bestellen
</div>
<div class="front">
	Sie wollen Ihre Karten <b>bequem von zu Hause</b> aus bestellen?<br />
	Sie haben <b>keine Vorverkaufsstelle in der Nähe</b>?<br />
	Kein Problem, <b>bestellen Sie einfach online</b>!
	<div class="trenner"></div>
	<p>Und so geht's:</p>
	<div class="instructions">
		<ul class="steps">
			<li><span>1.</span>Aufführungstermin aussuchen.</li>
			<li><span>2.</span>Anzahl der Karten wählen.</li>
			<li><span>3.</span>Ihren Namen hinterlassen.</li>
			<li><span>4.</span>Zahlungsmittel wählen.</li>
			<li><span>5.</span>Ihre Karten landen in Ihrem e-mail-Postfach.</li>
		</ul>
		<div class="box">
			<div class="top">
				Akzeptierte Zahlungsmittel
			</div>
			<div class="con">
				<ul>
					<li>
						<div>Lastschrift</div>
						Sie erhalten Ihre Karten sofort!
					</li>
					<li>
						<div>Überweisung</div>
						Sie erhalten Ihre Karten nach Zahlungseingang.
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>
{if $smarty.now < 1338760800}
<div class="hl" style="font-size: 24px;">
	Der Vorverkauf beginnt am 4. Juni
</div>
{else}
<div class="hcen">
	<a href="/tickets/bestellen"><img src="/gfx/order/order.png" alt="Jetzt bestellen" /></a>
</div>
{/if}
{include file="foot.tpl"}