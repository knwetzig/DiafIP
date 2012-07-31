<div><h4>{$film->titel}</h4>
	<table style='margin-left:30px'>
		<colgroup><col width='10%'><col></colgroup>
		{if $film->utitel}<tr><td>Untertitel:</td><td>{$film->utitel}</td></tr>{/if}
		{if $film->atitel}<tr><td>Arbeitstitel:</td><td>{$film->atitel}</td></tr>{/if}
		{if $film->stitel}<tr><td>Serientitel:</td><td {if $film->sdescr}{popup fgcolor="#ffffff" bgcolor="#400020" text="`$film->sdescr`" delay="1000"}{/if}>{$film->stitel} ({$film->sfolge})</td></tr>{/if}
		{if $film->gatt}<tr><td>Art:</td><td>{$art}</td></tr>{/if}
		{if $film->ezul}<tr><td>Zulassung:</td><td>{$film->ezul|date_format:"%d. %B %Y"}</td></tr>{/if}
		<tr><td>Inhalt:</td><td style='white-space:normal'>{$film->inhalt|nl2br}</td></tr>
		{if $film->memo}<tr><td>Notiz:</td><td>{$film->memo|nl2br}</td></tr>{/if}
	</table>
</div>
