{include file="head.tpl" title="Mitgliederbereich -  {$title}" cssfile="members"}
<div class="hl members">Mitgliederbereich</div>
{if $_user['id']}
<div class="sections">
	{if $_user['group'] == 2}
	<div class="main">
		{if $board}<a href="/mitglieder">{else}<span>{/if}Mitglieder{if $board}</a>{else}</span>{/if} | {if !$board}<a href="/mitglieder/tickets">{else}<span>{/if}Vorstand{if !$board}</a>{else}</span>{/if} | <a href="/mitglieder/login?action=logout">Logout</a>
	</div>
	{/if}
	<div class="sub">
		{foreach $subs as $sub}
		{$current=$sub['page'] != $smarty.server.REQUEST_URI}
		{if $current}<a href="{$sub['page']}">{/if}{$sub['title']}{if $current}</a>{/if}
		{/foreach}
	</div>
</div>
{/if}