{include file="head.tpl" title="Gästebuch"}
		Wir würden uns sehr über einen Eintrag in unserem Gästebuch freuen.
		<div style="position: relative;">
			<div style="text-align: right;">
				<a href="/gbook/new"><img src="/gfx/gbook/newentry.png" alt="eintragen" title="eintragen" /></a>
			</div>
			<div style="position: absolute; top: 15px; left: 10px; font-size: 16px; padding: 3px; width: 150px;">
				Seite: {$navi}
			</div>
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
		<div style="margin-left: 10px; margin-top: 10px; font-size: 16px; padding: 3px; width: 150px;">
			Seite: {$navi}
		</div>
{include file="foot.tpl"}