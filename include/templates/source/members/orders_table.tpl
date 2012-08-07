<div class="box{if $important && $orders['orders']|@count} important{/if}">
	<div class="top">
		{$title}
	</div>
	<div class="con">
		<a name="{$aName}"></a>
		{if $orders['orders']|@count}
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
			{foreach $orders['orders'] as $order}
			{$address=$order->getAddress()}
			<tr>
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
					{if $unpaid}<a href="bestellung?id={$order->getId()}&amp;action=markPaid&amp;goto=orders" class="markPaid"><img src="/gfx/members/unpaid.png" alt="markieren als bezahlt" title="markieren als bezahlt" /></a> &nbsp;{/if}
					<a href="bestellung?id={$order->getId()}"><img src="/gfx/members/details.png" alt="Details anzeigen" title="Details anzeigen" /></a>
				</td>
			</tr>
			{/foreach}
		</table>
		{if $orders['more']}
		<div class="hcen more">
			<div>...</div>
			<a href="?showAll=1#{$aName}">Alle Bestellungen anzeigen</a>
		</div>
		{/if}
		{else}
		Keine Bestellungen vorhanden.
		{/if}
	</div>
</div>