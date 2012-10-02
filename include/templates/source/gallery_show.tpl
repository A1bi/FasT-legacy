{include file="head.tpl" title="Fotogalerie - {$gallery.title}" cssfile="gallery" jsfiles=[["gallery", 0]] head="gallery_head.tpl" noSlides=true}
		<div class="picFrame">
			<div class="pic"></div>
			<div class="directions">
				<div class="next">
					<div class="space"></div>
					<div class="arrow"></div>
				</div>
				<div class="prev">
					<div class="space"></div>
					<div class="arrow"></div>
				</div>
			</div>
			<div class="bar">
				<div class="number">Foto <span></span> von <span></span></div>
				<div class="desc"></div>
			</div>
		</div>
		<div class="info">
			<div class="desc"></div>
			<div class="disclaimer small">
				{$gallery.copyright|escape}
			</div>
		</div>
{include file="foot.tpl"}