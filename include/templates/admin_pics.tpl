{include file="head.tpl" title="Admin"}
		{if $smarty.get.id != ""}
		<form method="post" action="pics.php?id={$smarty.get.id|escape}" enctype="multipart/form-data">
		<div style="text-align: center; font-size: 14px;"><strong>{$msg}</strong></div>
		<div>
			<table width="60%" align="center" style="font-size: 14px;">
				<tr>
					<td width="20%">Bild:</td>
					<td width="80%"><input type="file" name="file" /></td>
				</tr>
				<tr>
					<td width="20%">Beschreibung:</td>
					<td width="80%"><input type="text" name="desc" /></td>
				</tr>
			</table>
			<div style="text-align: center;">
				<input type="submit" name="submit" value="hochladen" />
			</div>
		</div>
		</form>
		<div class="hcen" style="margin: 10px;"><a href="pics.php?id={$smarty.get.id|escape}&amp;import=1">Massen-Import aus Ordner</a></div>
		{else}
		<div style="font-size: 14px;">
			Bitte wählen Sie eine Gallerie, in die Sie die Fotos einfügen möchten:
			{foreach $galleries as $gallery}
			<div style="text-align: center;"><a href="pics.php?id={$gallery.id}">{$gallery.title|escape}</a></div>
			{/foreach}
		</div>
		{/if}
{include file="foot.tpl"}