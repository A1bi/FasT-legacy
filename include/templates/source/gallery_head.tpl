	<script>
		(function () {
			var g = new Gallery("{$gallery['id']}");
			g.addPics([
{foreach $pics as $pic}
				{literal}{{/literal}id: "{$pic['id']}", text: "{$pic['text']|escape}"}{if !$pic@last},{/if}

{/foreach}
			]);
			g.init();
		})();
	</script>