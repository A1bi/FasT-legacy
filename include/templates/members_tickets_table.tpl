<div class="box{if $important && $orders|@count} important{/if}">
	<div class="top">
		{$title}
	</div>
	<div class="con">
		{if $orders|@count}
		<table>
			<tr class="title">
				<td>ON</td>
				<td>Datum</td>
				<td>Name</td>
				<td>Karten</td>
				<td>Betrag</td>
				<td></td>
			</tr>
			{foreach $orders as $order}
			{$address=$order->getAddress()}
			<tr>
				<td>{$order->getSId()}</td>
				<td>{$order->getTime()|date_format:"%d.%m.%y, %H.%M Uhr"}</td>
				<td>{$address['firstname']} {$address['lastname']}</td>
				<td>{$order->getTickets()|@count}</td>
				<td>{$order->getTotal()} €</td>
				<td><a href="/mitglieder/tickets?order={$order->getId()}&action=showDetails">Details</a></td>
			</tr>
			{/foreach}
		</table>
		{else}
		Keine Bestellungen vorhanden.
		{/if}
	</div>
</div>