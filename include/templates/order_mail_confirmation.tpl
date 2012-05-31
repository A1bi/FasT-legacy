Vielen Dank für Ihre Bestellung, {$address['firstname']} {$address['lastname']}!

Folgende Karten haben Sie für die Aufführung am {$tickets[0]->getDateString()} bestellt:

{foreach $tickets as $ticket}
{$ticket@iteration}. {if $ticket->getType() == 1}Erwachsener{else}Kind{/if} - {$ticket->getPrice()} €
{/foreach}
----------------------------
Gesamtbetrag: {$order->GetTotal()} €

{if $payment['method'] == "charge"}
Der Betrag wird in den kommenden Tagen von dem von Ihnen angegebenen Konto abgebucht.

Ihre Karten werden in wenigen Momenten auf dieser e-mail-Adresse eintreffen!
{else}
Bitte überweisen Sie den Betrag von {$order->GetTotal()} € auf folgendes Konto:

Kontoinhaber: Freilichtbühne am schiefen Turm e.V.
Kontonummer: 178167
BLZ: 57069144
Kreditinstitut: Raiffeisenbank Kaisersesch
{/if}


Wir wünschen Ihnen viel Spaß bei der Aufführung!

Die Freilichtbühne am schiefen Turm Kaisersesch