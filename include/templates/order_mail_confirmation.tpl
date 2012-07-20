{$tickets=$order->getTickets()}
{$payment=$order->getPayment()}
Sehr geehrte{if $address['gender'] == 1} Frau{else}r Herr{/if} {$address['lastname']},

vielen Dank für Ihre Bestellung!

Folgende Karten haben Sie für die Aufführung am {$tickets[0]->getDateString()} bestellt:

{foreach $tickets as $ticket}
{$ticket@iteration}. {if $ticket->getType() == 1}Erwachsener{else}Ermäßigt{/if} - {$ticket->getPrice()} €
{/foreach}
----------------------------
Gesamtbetrag: {$order->GetTotal()} €

{if $payment['method'] == OrderPayMethod::Charge}
Der Betrag wird in den kommenden Tagen von dem von Ihnen angegebenen Konto abgebucht.

Ihre Karten werden in wenigen Momenten auf dieser e-mail-Adresse eintreffen!
{else}
Bitte überweisen Sie den Betrag von {$order->GetTotal()} € auf folgendes Konto:

{include file="order_mail_bankdetails.tpl"}
{/if}


Wir wünschen Ihnen viel Spaß bei der Aufführung!

Die Freilichtbühne am schiefen Turm Kaisersesch