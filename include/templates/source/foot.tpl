			</div>
			<div class="bottom">
				<div>
					&copy; 2009 - 2012 Freilichtbühne am schiefen Turm e.V. - <a href="/impressum">Impressum</a> - <a href="/satzung">Satzung</a> - <a href="/agb">AGB</a>
				</div>
			</div>
		</div>
	</div>
{if !$_config.dev && $_config.as_site && !$smarty.server.HTTPS}

	<script>var as_site = {$_config.as_site};</script>
	<script src="http://system.albisigns.de/stats.js"></script>
	<noscript><img src="http://system.albisigns.de/stats{$_config.as_site}.png" alt="" /></noscript>

{/if}
</body>
</html>