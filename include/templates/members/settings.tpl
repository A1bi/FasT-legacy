{include file="members/head.tpl" title="Einstellungen"}
<div class="hl section">Einstellungen</div>

<div class="box settings">
	<div class="top">Passwort ändern</div>
	<div class="con">
		<form action="einstellungen" method="post">
		<div class="msg hcen">{$msg}</div>
		<table class="inner">
			<tr>
				<td class="caption">Altes Passwort:</td>
				<td><input type="password" name="old" /></td>
			</tr>
		</table>
		<div class="inner newSection"></div>
		<table class="inner">
			<tr>
				<td class="caption">Neues Passwort:</td>
				<td><input type="password" name="new1" /></td>
			</tr>
			<tr>
				<td class="caption">Neues Passwort noch einmal:</td>
				<td><input type="password" name="new2" /></td>
			</tr>
		</table>
		<div class="hcen">
			<input type="submit" name="submit" value="ändern" class="btn" />
		</div>
		</form>
	</div>
</div>
{include file="foot.tpl"}