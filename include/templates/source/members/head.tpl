{append "cssfiles" "members/main"}
{include file="head.tpl" title="Mitgliederbereich -  {$title}" noSlides=true}
{$sections['members'] = ["Mitglieder", [
	["index", "", "Hauptseite"], ["settings", "einstellungen", "Einstellungen"]
]]}
{if $_user['group'] == 2}
{$sections['board'] = ["Vorstand", [
	["orders", "bestellungen", "Ticketbestellungen"], ["free", "freikarten", "Freikarten"], ["stats", "statistik", "Ticketstatistik"]
]]}
{/if}
<div class="navbar box">
	<div class="top">
		<div class="hl">Mitgliederbereich</div>
{if $_user['id']}
		<div class="userinfo">
			Hallo {$_user['firstname']|escape}! | <a href="/mitglieder/login?action=logout">Logout</a>
		</div>
{/if}
	</div>
{if $_user['id']}
	<div class="con">
{foreach $sections as $section}
		<div class="sections">
			<div class="name">{$section[0]}</div>
{foreach $section[1] as $page}
			<div class="section">
				<a href="/mitglieder/{$page[1]}"{if $pageBelongsTo == $page[0] || basename($smarty.server.SCRIPT_FILENAME, ".php") == $page[0]} class="current"{/if}>{$page[2]}</a>
			</div>
{/foreach}
		</div>
{/foreach}
	</div>
{/if}
</div>