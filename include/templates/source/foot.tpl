			</div>
			<div class="bottom">
				<div>
					&copy; 2009 - 2012 Freilichtbühne am schiefen Turm e.V. - <a href="/impressum">Impressum</a> - <a href="/satzung">Satzung</a> - <a href="/agb">AGB</a>
				</div>
			</div>
		</div>
	</div>
{if !$_config.dev && $_config.as_site}
{$baseUrl="http{if $smarty.server.HTTPS}s{/if}://system.albisigns.de{if $smarty.server.HTTPS}:442{/if}/"}

	<script>var as_site = {$_config.as_site};</script>
	<script src="{$baseUrl}stats.js"></script>
	<noscript><img src="{$baseUrl}stats{$_config.as_site}.png" alt="" /></noscript>

{/if}
</body>
</html>