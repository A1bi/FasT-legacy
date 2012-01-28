{include file="head.tpl" title="Admin - Login"}
		<form method="post" action="login.php">
		<div style="text-align: center; font-size: 14px;">
			<div style=\"color: #ff0000;\"><strong>{$msg}</strong></div>
			Name: <input type="text" name="name" value="{$smarty.post.name|escape}" /><br /><br />Passwort: <input type="password" name="pass" />
			<p><input type="submit" name="login" value="login" /></p>
		</div>
		</form>
{include file="foot.tpl"}