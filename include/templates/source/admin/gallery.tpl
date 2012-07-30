{include file="head.tpl" title="Admin"}
		{if $success}
		<div style="text-align: center;">
			<img src="/gfx/gbook/success.jpg" alt="Vielen Dank!" />
			<p>Die Gallerie wurde hinzugef&uuml;gt!</p>
			<p>Sie k&ouml;nnen nun <a href="pics.php?id={$id}">hier</a> Fotos hochladen.</p>
		</div>
		{else}
		<form method="post" action="gallery.php">
		<div>
			<table width="60%" align="center" style="font-size: 14px;">
				<tr>
					<td width="20%">Titel:</td>
					<td width="80%"><input type="text" name="title" /></td>
				</tr>
				<tr>
					<td width="20%">Coypright-Hinweis:</td>
					<td width="80%"><input type="text" name="copyright" /></td>
				</tr>
			</table>
			<div style="text-align: center;">
				<input type="submit" name="submit" value="speichern" />
			</div>
		</div>
		</form>
		{/if}
{include file="foot.tpl"}