{include file="members_head.tpl" title="Login"}
<div class="hl section">Bitte einloggen!</div>
<div class="trenner"></div>
<form method="post" action="login" class="login">
<div class="msg hcen">{$msg}</div>
<table class="inner">
	<tr>
		<td>Name:</td>
		<td><input type="text" name="name" value="{$smarty.post.name|escape}" /></td>
	</tr>
	<tr>
		<td>Passwort:</td>
		<td><input type="password" name="pass" /></td>
	</tr>
</table>
<div class="hcen">
	<input type="submit" name="login" value="login" class="btn" />
</div>
</form>
<div class="trenner"></div>
{include file="foot.tpl"}