{include file="head.tpl" title="Fotogalerie" cssfile="gallery"}
		<div class="hl">
			<img src="/gfx/gallery/hl.png" alt="Galerien" />
		</div>
		{foreach $galleries as $gallery}
		<div class="box">
			<div class="top">
				{$gallery.title}
			</div>
			<div class="con">
				<a href="/gallery/{$gallery.id}">
				{foreach $gallery.pics as $pic}
					<div class="pic{if $pic@first} first{elseif $pic@last} last{/if}" style="background-image: url(/gfx/cache/gallery/{$gallery.id}/small/{$pic.id}.jpg);"></div>
				{/foreach}
				</a>
			</div>
		</div>
		{/foreach}
{include file="foot.tpl"}