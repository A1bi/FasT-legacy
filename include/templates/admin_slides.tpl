{include file="head.tpl" title="Admin"}
		<form method="post" action="slides.php" enctype="multipart/form-data">
		<div style="text-align: center; font-size: 14px;"><strong>{$msg}</strong></div>
		<div>
			<table width="60%" align="center" style="font-size: 14px;">
				<tr>
					<td width="20%">Bild:</td>
					<td width="80%"><input type="file" name="file" /></td>
				</tr>
			</table>
			<div style="text-align: center;">
				<input type="submit" name="submit" value="hochladen" />
			</div>
		</div>
		</form>
{include file="foot.tpl"}