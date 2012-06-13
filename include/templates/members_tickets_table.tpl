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
				<td class="sId">{$order->getSId()}</td>
				<td>{$order->getTime()|date_format:"%d.%m.%y, %H.%M Uhr"}</td>
				<td>{$address['firstname']} {$address['lastname']}</td>
				<td>{$order->getTickets()|@count}</td>
				<td>{$order->getTotal()} €</td>
				<td class="actions">
					{if $unpaid}<a href="/mitglieder/tickets?order={$order->getId()}&action=markPaid&goto=overview"><img src="/gfx/members/unpaid.png" alt="markieren als bezahlt" title="markieren als bezahlt" /></a> &nbsp;{/if}
					<a href="/mitglieder/tickets?order={$order->getId()}&action=showDetails&goto=overview"><img src="/gfx/members/details.png" alt="Details anzeigen" title="Details anzeigen" /></a>
				</td>
			</tr>
			{/foreach}
		</table>
		{else}
		Keine Bestellungen vorhanden.
		{/if}
	</div>
</div>