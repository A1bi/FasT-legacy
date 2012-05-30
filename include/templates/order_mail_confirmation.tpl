Vielen Dank für Ihre Bestellung, {$order['address']['firstname']} {$order['address']['lastname']}!

Folgende Karten haben Sie für {$order['date']} bestellt:

{$order.number['kids']} Kind{if $order.number.kids != 1}er{/if} für je {$prices['kids']} € (Gesamt: {$order['number']['kids'] * $prices['kids']} €)
{$order.number['adults']} Erwachsene{if $order['number']['adults'] == 1}r{/if} für je {$prices['adults']} € (Gesamt: {$order['number']['adults'] * $prices['adults']} €)
Gesamtbetrag: {$order['total']} €

{if $order.payment.method == "charge"}
Der Betrag wird in den kommenden Tagen von dem von Ihnen angegebenen Konto abgebucht.

Ihre Karten werden in wenigen Momenten auf dieser e-mail-Adresse eintreffen!
{else}
Bitte überweisen Sie den Betrag von {$order['total']} € an folgendes Konto:
Kontoinhaber: Freilichtbühne am schiefen Turm e.V.
Kontonummer: 178167
BLZ: 57069144
Kreditinstitut: Raiffeisenbank Kaisersesch
{/if}


Wir wünschen Ihnen viel Spaß bei der Aufführung!

Die Freilichtbühne am schiefen Turm Kaisersesch