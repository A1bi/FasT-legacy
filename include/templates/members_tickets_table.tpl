<div class="box">
	<div class="top">
		{$title}
	</div>
	<div class="con">
		{if $orders|@count > 0}
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
			<tr>
				<td>{$order['sId']}</td>
				<td>{$order['time']|date_format:"%d.%m.%y, %H.%M Uhr"}</td>
				<td>{$order['firstname']} {$order['lastname']}</td>
				<td>{$order['tickets']}</td>
				<td>{$order['total']} €</td>
				<td><a href="/mitglieder/tickets?order={$order['id']}&action=showDetails">Details</a></td>
			</tr>
			{/foreach}
		</table>
		{else}
		Keine Bestellungen vorhanden.
		{/if}
	</div>
</div>