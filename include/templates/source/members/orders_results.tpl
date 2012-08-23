<div class="box results">
	<div class="top">
		{$title}
	</div>
	<div class="con">
		<div class="rows">
			<table>
				<tr class="title">
					<td>ON</td>
					<td>Zeitpunkt</td>
					<td>Name</td>
					<td>Karten</td>
					<td>Betrag</td>
					{if $unpaid}<td>ausstehend</td>{/if}
					<td></td>
				</tr>
			</table>
			<div class="pageNav">Seite: <span class="pages"></span></div>
		</div>
		<em class="noRows small">
			Keine Buchungen vorhanden.
		</em>
	</div>
</div>