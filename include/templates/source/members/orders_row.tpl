{$address=$order->getAddress()}
<tr class="row">
	<td class="sId">{$order->getSId()}</td>
	<td>{$order->getTime()|date_format_x:"%@, %H.%M Uhr"}</td>
	<td>
		{$name={"{$address['firstname']} {$address['lastname']}"|escape}}
		{$name}
		{if $address['affiliation']}
			{if $name != " "}<br />({/if}{$address['affiliation']|escape}{if $name != " "}){/if}
		{/if}
	</td>
	<td>{$order->getNumberOfValidTickets()}</td>
	<td>{$order->getTotal()} €</td>
	{if $unpaid}{$difference={$order->getTime()|time_difference}}<td>seit {$difference} Tag{if $difference != 1}en{/if}</td>{/if}
	<td class="actions">
		{if $unpaid}<a href="buchungen/{$order->getId()}&amp;action=markPaid&amp;goto=orders" class="markPaid"><img src="/gfx/members/unpaid.png" alt="markieren als bezahlt" title="markieren als bezahlt" /></a> &nbsp;{/if}
		<a href="/mitglieder/buchungen/{$order->getId()}"><img src="/gfx/members/details.png" alt="Details anzeigen" title="Details anzeigen" /></a>
	</td>
</tr>