{include file="head.tpl" title="Fotogalerie - {$gallery.title}" cssfile="gallery" noSlides=true}
		{if $smarty.get.pic != $pics}
		<div class="nextpic">
			<a href="/gallery/{$pic.gallery}/{$smarty.get.pic+1}#pic"><img src="/gfx/gallery/nextpic.jpg" alt="Nächstes Foto" /></a><br />
		</div>
		{/if}
		{if $smarty.get.pic != 1}
		<div class="prevpic">
			<a href="/gallery/{$pic.gallery}/{$smarty.get.pic-1}#pic"><img src="/gfx/gallery/prevpic.jpg" alt="Vorheriges Foto" /></a><br />
		</div>
		{/if}
		<div class="fullpic">
			<a name="pic" href="/gallery/{$pic.gallery}/{if $smarty.get.pic != $pics}{$smarty.get.pic+1}{else}1{/if}#pic"><img src="/gfx/cache/gallery/{$pic.gallery}/medium/{$pic.id}.jpg" alt="" /></a>
			<div style="margin-top: 10px;">
				{$pic.text|escape}
			</div>
			<div style="margin-top: 10px; font-size: 13px; color: #cccccc;">
				{$gallery.copyright}
			</div>
			<div class="picnavi">
				{$navi}
			</div>
		</div>
{include file="foot.tpl"}