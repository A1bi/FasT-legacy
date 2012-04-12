<!DOCTYPE html>
<html>
<head>
	<title>Freilichtbühne am schiefen Turm e.V.{if $title != ""} - {$title}{/if}</title>
	<meta charset="utf-8" />
	<meta name="keywords" content="Theater, Freilichtbühne, Verein, Kaisersesch{$meta_keys}" />
	<link rel="shortcut icon" href="/gfx/favicon.ico" />
	<link rel="stylesheet" type="text/css" href="/css/main.css{fileVersion file="/css/main.css"}" />
	<script src="http://system.albisigns.de/js/lib/jquery.js"></script>
	<script src="/gfx/cache/slides.js{fileVersion file="/gfx/cache/slides.js"}"></script>
	<script src="/js/main.js{fileVersion file="/js/main.js"}"></script>
{if $css != ""}
	<style type="text/css">
{include file=$css}

	</style>
	
{/if}
{if $cssfile != ""}
	<link rel="stylesheet" type="text/css" href="/css/{$cssfile}.css{fileVersion file="/css/{$cssfile}.css"}" />	  
{/if}
{if $jsfile != ""}
	<script src="/js/{$jsfile}.js{fileVersion file="/js/{$jsfile}.js"}"></script>
{/if}
{if $head != ""}
{include file=$head}
	
{/if}
</head>

<body>
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
				<li><a href="/geschichte">Unsere Geschichte</a></li>
				<li class="spacer"></li>
				<li><a href="/theater">Theater</a></li>
				<li class="spacer"></li>
				<li><a href="/termine">Termine</a></li>
				<li class="spacer"></li>
				<li><a href="/gallery">Fotogalerie</a></li>
				<li class="spacer"></li>
				<li><a href="/gbook">Gästebuch</a></li>
			</ul>
			<div class="bottom"></div>
		</div>
	</div>
	<div id="right">
		<div id="content">
			<div class="top"></div>
			<div class="content">
				<div>