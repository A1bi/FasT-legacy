<div class="box{if $important && $orders|@count} important{/if}">
	<div class="top">
		{$title}
	</div>
	<div class="con">
		{if $orders|@count}
		<table>
			<tr class="title">
				<td>ON</td>
				<td>Zeitpunkt</td>
				<td>Name</td>
				<td>Karten</td>
				<td>Betrag</td>
				{if $unpaid}<td>ausstehend</td>{/if}
				<td></td>
			</tr>
			{foreach $orders as $order}
			{$address=$order->getAddress()}
			<tr>
				<td class="sId">{$order->getSId()}</td>
				<td>{$order->getTime()|date_format_x:"%@, %H.%M Uhr"}</td>
				<td>{"{$address['firstname']} {$address['lastname']}"|escape}</td>
				<td>{$order->getNumberOfValidTickets()}</td>
				<td>{$order->getTotal()} €</td>
				{if $unpaid}{$difference={$order->getTime()|time_difference}}<td>seit {$difference} Tag{if $difference != 1}en{/if}</td>{/if}
				<td class="actions">
					{if $unpaid}<a href="/mitglieder/tickets?order={$order->getId()}&amp;action=markPaid&amp;goto=overview" class="markPaid"><img src="/gfx/members/unpaid.png" alt="markieren als bezahlt" title="markieren als bezahlt" /></a> &nbsp;{/if}
					<a href="/mitglieder/tickets?order={$order->getId()}&amp;action=showDetails"><img src="/gfx/members/details.png" alt="Details anzeigen" title="Details anzeigen" /></a>
				</td>
			</tr>
			{/foreach}
		</table>
		{else}
		Keine Bestellungen vorhanden.
		{/if}
	</div>
</div>