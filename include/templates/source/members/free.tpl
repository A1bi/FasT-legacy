{include file="members/head.tpl" title="Freikarten"}
{$gender=["", "Frau", "Herr"]}
<div class="hl section">Freikarten</div>

<div class="free">
	<div class="box new">
		<div class="top">
			<a href="?action=new">Freikarten reservieren</a>
		</div>
	</div>
	
	<div class="box">
		<div class="top">
			Reservierte Freikarten
		</div>
		<div class="con">
			{if $orders|@count}
			<table>
				<tr class="title">
					<td>Name</td>
					<td>Aufführung</td>
					<td>Karten</td>
				</tr>
				{foreach $orders as $order}
				{$address=$order->getAddress()}
				{$tickets=$order->getTickets()}
				<tr>
					<td>
					{if $address['affiliation'] != ""}
						{$address['affiliation']|escape}
					{else}
						{if $address['firstname'] != ""}{$address['firstname']|escape}
						{else}{$gender[$address['gender']]}{/if} {$address['lastname']|escape}
					{/if}
					</td>
					<td>{$tickets[0]->getDateString()}</td>
					<td>{$order->getNumberOfValidTickets()}</td>
				</tr>
				{/foreach}
			</table>
			{else}
			Keine Reservierungen vorhanden.
			{/if}
		</div>
	</div>
</div>
{include file="foot.tpl"}