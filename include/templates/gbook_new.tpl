{include file="head.tpl" title="Gästebuch - Neuer Eintrag"}
		<form action="/gbook/new" method="post">
		<input type="hidden" name="codenr" value="{$code}" />
		<br />
		<div class="box">
			<div class="top">
				Neuer Eintrag
			</div>
			<div class="con">
				<div style="color: #ff0000; text-align: center;" id="errors">{$msg}</div>
				<table width="100%">
					<tr style="height: 35px;">
						<td width="25%">Name:</td>
						<td width="75%"><input type="text" name="name" style="width: 250px;" id="namefield" value="{$smarty.post.name|escape}" /></td>
					</tr>
					<tr style="height: 140px;">
						<td style="vertical-align: top;">Text:</td>
						<td><textarea name="text" style="width: 350px; height: 120px;" rows="100" cols="100" id="textfield">{$smarty.post.text|escape}</textarea></td>
					</tr>
					<tr style="height: 35px;">
						<td>Sicherheitscode:<br /><img src="http://system.albisigns.de/gfx/captcha/{$code}.jpg" alt="" /></td>
						<td><input type="text" name="code" id="codefield" style="width: 150px;" />
							<div style="font-size: 10px; margin-top: 4px;">Geben Sie einfach die vier Zeichen aus der Grafik in dieses Feld ein.</div>
						</td>
					</tr>
				</table>
				<div style="text-align: center; margin-top: 30px;">
					<input type="submit" name="submit" value="eintragen" style="width: 100px; height: 25px;" id="subbutton" />
				</div>
			</div>
		</div>
		</form>
{include file="foot.tpl"}