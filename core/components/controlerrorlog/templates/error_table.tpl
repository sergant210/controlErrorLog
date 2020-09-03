<table class="error-log-table">
	<thead>
	<tr>
		<th><i class="celicon celicon-minus-square toggle" id="toggle-total" onclick="controlErrorLog.toggleAll(this)"></i></th>
		<th class="date">{$lexicon['date']}</th>
		<th class="time">{$lexicon['time']}</th>
		<th class="type">{$lexicon['type']}</th>
        {if $defExists}
			<th class="type">{$lexicon['def']}</th>
        {/if}
		<th>{$lexicon['file']}</th>
		<th>{$lexicon['line']}</th>
	</tr>
	</thead>
	<tbody>
    {foreach $messages as $message}
		<tr id="elt-row{$message@iteration}" class="{$message->type|lower}-message error-data {if $message->type eq 'FATAL'}text-error{/if}">
			<td><div class="type-border"></div><i class="celicon celicon-minus-square error-data toggle" onclick="controlErrorLog.toggle(this)"></i></td>
			<td>{$message->date|date_format:"{$dateFormat}"}</td>
			<td>{$message->time}</td>
			<td>{$message->type}</td>
            {if $defExists}
				<td>{$message->def}</td>
            {/if}
			<td>{$message->file}</td>
			<td>{$message->line}</td>
		</tr>
		<tr id="elt-row{$message@iteration}-message" class="{$message->type|lower}-message error-description {if $message->type eq 'FATAL'}text-error{/if}">
			<td colspan="{if $defExists}7{else}6{/if}"><div class="type-border"></div>
				<pre>{$message->message}</pre>
			</td>
		</tr>
    {/foreach}
	</tbody>
</table>