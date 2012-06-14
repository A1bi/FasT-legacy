Hallo {$address['firstname']} {$address['lastname']}!

{if $gotPaid}Vielen Dank für Ihre Zahlung!{/if}

Ihre Karten stehen nun zum Download als PDF-Dokument bereit unter:
https://{$smarty.server.SERVER_NAME}/media/tickets/{$order->getHash()}.pdf

Alle Hinweise zur Verwendung finden Sie unterhalb der Tickets.


Die Freilichtbühne am schiefen Turm