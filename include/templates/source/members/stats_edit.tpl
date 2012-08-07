{include file="members/head.tpl" title="Ticketstatistik bearbeiten"}
<div class="hl section">Ticketstatistik bearbeiten</div>

<div class="tickets">
	<form action="?action=editRetail&retail={$smarty.get.retail|escape}" method="post">
	<div class="box stats">
		<div class="top">
			Statistik für {$retail}
		</div>
		<div class="con">
			<table>
				<tr class="title">
					<td>Aufführung</td>
{foreach OrderManager::$theater['prices'] as $price}
{if $price['type'] == "free"}{continue}{/if}
					<td>{$price['desc']}</td>
{/foreach}
				</tr>
				{foreach OrderManager::$theater['dates'] as $date}
				<tr>
					<td class="left">{OrderManager::getStringForDate($date)}</td>
{foreach OrderManager::$theater['prices'] as $price}
{if $price['type'] == "free"}{continue}{/if}
					<td><input type="tel" name="number[{$date@key}][{$price@key}]" /></td>
{/foreach}
				</tr>
				{/foreach}
			</table>
			<p>Die eingegebenen Werte werden zu den aktuellen addiert.</p>
			<div class="hcen"><input type="submit" name="edit" value="speichern" class="btn" /></div>
		</div>
	</div>
	</form>
</div>
{include file="foot.tpl"}