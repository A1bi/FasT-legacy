{include file="head.tpl" title="Gästebuch" cssfile="gbook"}
		<div class="hl">
			Gästebuch
		</div>
		Wir würden uns sehr über einen Eintrag in unserem Gästebuch freuen.
		<div class="new">
			<a href="/gbook/new">eintragen</a>
		</div>
		<div class="pages">
			Seite: {$navi}
		</div>
		{foreach $entries as $entry}
			<div class="box">
				<div class="top">
					{$entry.name|escape}
				</div>
				<div class="con">
					<div style="text-align: right; font-size: 12px;">
						am {$entry.time|date_format:"%d.%m.%Y"} um {$entry.time|date_format:"%H:%I"} Uhr
					</div>
					{$entry.text|escape|nl2br}
				</div>
			</div>
		{/foreach}
		<div class="pages">
			Seite: {$navi}
		</div>
{include file="foot.tpl"}