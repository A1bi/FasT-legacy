{if $jsfile != ""}{$jsfiles = [[$jsfile, 0]]}{/if}
{if $cssfile != ""}{$cssfiles = [$cssfile]}{/if}
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
	<meta name="keywords" content="Theater, Freilichtbühne, Verein, Kaisersesch{$meta_keys}" />
	<title>Freilichtbühne am schiefen Turm e.V.{if $title != ""} - {$title}{/if}</title>
	<link rel="shortcut icon" href="/gfx/favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="{"/css/main.css"|append_version}" />
	<script src="/core/js/jquery.js"></script>
	<script src="{"/gfx/cache/slides.js"|append_version}"></script>
	<script src="{"/js/main.js"|append_version}"></script>

{foreach $cssfiles as $cssfile}
	<link rel="stylesheet" type="text/css" href="{"/css/{$cssfile}.css"|append_version}" />
{/foreach}
{foreach $jsfiles as $jsfile}
{if $jsfile[1] == 0}
{$file = {"/js/{$jsfile[0]}.js"|append_version}}
{elseif $jsfile[1] == 1}
{$file = "/core/js/{$jsfile[0]}.js"}
{else}
{$file = $jsfile[0]}
{/if}
	<script src="{$file}"></script>
{/foreach}
{if $head != ""}

{include file=$head}

{/if}
</head>

<body{if $noSlides} class="noSlides"{/if}>
	<div id="sky"></div>
	<div id="slides">
		<div class="slide finished"></div>
		<div class="slide finished"></div>
		<div class="fg"></div>
	</div>
	<div id="left">
		<a href="/" id="logo">Freilichtbühne am schiefen Turm</a>
		<div id="navi">
			<ul>
				<li><a href="/">Home</a></li>
				<li class="spacer"></li>
				<li><a href="/termine">Termine</a></li>
				<li class="spacer"></li>
				<li><a href="/info">Informationen</a></li>
				<li class="spacer"></li>
				<li><a href="/geschichte">Geschichte</a></li>
				<li class="spacer"></li>
				<li><a href="/theater">Theaterstücke</a></li>
				<li class="spacer"></li>
				<li><a href="/galerie">Fotogalerie</a></li>
				<li class="spacer"></li>
				<li><a href="/gästebuch">Gästebuch</a></li>
			</ul>
			<div class="bottom"></div>
		</div>
	</div>
	<div id="right">
		<div id="content">
			<div class="top"></div>
			<div class="content">