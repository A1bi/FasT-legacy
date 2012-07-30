Sehr geehrte{if $address['gender'] == 1} Frau{else}r Herr{/if} {$address['lastname']},

leider konnten wir zur Ihrer Bestellung (ON{$order->getSId()}) bisher keinen Zahlungseingang feststellen.
Wir möchten Sie daher daran erinnern, den Betrag von {$order->GetTotal()} € auf folgendes Konto zu überweisen:

{include file="order_mail_bankdetails.tpl"}

Sollten Sie den Betrag bereits zwischenzeitlich überwiesen haben, sehen Sie diese Nachricht bitte als gegenstandslos an.

Mit freundlichen Grüßen
Die Freilichtbühne am schiefen Turm Kaisersesch