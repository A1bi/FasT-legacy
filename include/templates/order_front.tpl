{include file="head.tpl" title="Karten bestellen" cssfile="order"}
<div class="hl">
	Karten online bestellen
	<br />Wie funktioniert's?
</div>
<div class="front">
	Seit dieser Saison bietet Ihnen die Freilichtbühne am schiefen Turm die Möglichkeit, Ihre Karten für unsere Vorstellungen auch online zu bestellen! So können Sie sich innerhalb von Minuten bequem Ihre Karten sichern.
	<br />Dies ist besonders attraktiv für unsere Besucher, die von weiter her anreisen und daher keine Vorverkaufsstelle in der Nähe haben.
	<p>Die folgenden Schritte zeigen, wie einfach es geht:</p>
	<ul>
		<li><b>1.</b> Suchen Sie sich Ihre gewünschte Vorstellung aus und wählen Sie die Anzahl der Karten.</li>
		<li><b>2.</b> Folgenden Sie den Anweisungen und geben Sie einige Infos an, damit wir Sie notfalls für Rückfragen erreichen können.</li>
		<li><b>3.</b> Zahlen Sie per <b>Überweisung</b> oder bequem per <b>Lastschrift</b>. Bei letzterer Variante <b>erhalten Sie Ihre Karten sogar sofort</b> nach der Bestellung.</li>
		<li><b>4.</b> Sie erhalten schließlich eine e-mail mit Ihren Karten, die Sie sich anschließend ausdrucken können.</li>
	</ul>
</div>
{if $smarty.now < 1338760800}
<div class="hl" style="font-size: 24px;">
	Der Vorverkauf beginnt am 4. Juni
</div>
{else}
<div class="btns hcen">
	<a href="/tickets/bestellen" class="btn">Jetzt bestellen</a>
</div>
{/if}
{include file="foot.tpl"}