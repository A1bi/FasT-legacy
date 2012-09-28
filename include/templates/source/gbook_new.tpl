{include file="head.tpl" title="Gästebuch - Neuer Eintrag" cssfile="gbook"}
		<form action="/gästebuch/new" method="post">
		<input type="hidden" name="codenr" value="{$code}" />
		<br />
		<div class="box">
			<div class="top">
				Neuer Eintrag
			</div>
			<div class="con">
				<div id="errors">{$msg}</div>
				<table>
					<tr class="name">
						<td>Ihr Name:</td>
						<td><input type="text" name="name" value="{$smarty.post.name|escape}" required /></td>
					</tr>
					<tr>
						<td>Ihre Nachricht:</td>
						<td><textarea name="text" required>{$smarty.post.text|escape}</textarea></td>
					</tr>
					<tr>
						<td>
							Sicherheitscode:
							<br /><img src="http://system.albisigns.de/gfx/captcha/{$code}.jpg" alt="" />
						</td>
						<td class="code">
							<input type="text" name="code" required />
							<div>Geben Sie einfach die vier Zeichen aus der Grafik in dieses Feld ein.</div>
						</td>
					</tr>
				</table>
				<div class="submit">
					<input type="submit" name="submit" value="eintragen" />
				</div>
			</div>
		</div>
		</form>
{include file="foot.tpl"}