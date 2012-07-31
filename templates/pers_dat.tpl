{**************************************************************
Aufruf von
  pers_class.php
  class: Person
  proc:  view
	param:
        dialog[???][0] feldname
                   [1] inhalt (evt. weitere arrays)
                   [2] label
                   [3] Tooltip (soweit vorhanden)

**************************************************************}

{* Kopfzeile *}
<table width="100%"><tr>
    <td>
        <div style='white-space:normal' class='fett'>
        {if isset($dialog['vname'])}
            {$dialog['vname'][1]}&nbsp;
        {/if}
        {if isset($dialog['name'])}
            {$dialog['name'][1]}
        {/if}
        {if isset($dialog['aliases'])}
            <span style='font-weight:normal'>&nbsp;(
            {foreach from=$dialog['aliases'][1] item=alias}{$alias}&nbsp;{/foreach})
        </span>{/if}</div>
    </td>

    <td style="text-align:right;">
    {* Bearbeitungssymbole *}
        <form method='post'>
            {if isset($dialog['edit'])}
                <button
                    class='small'
                    name='aktion'
                    value='edit'><img src='images/edit.png' /></button>
            {/if}
            {if isset($dialog['del'])}
                <button
                    class='small'
                    name='aktion'
                    value='del' /><img src='images/del.png' /></button>
            {/if}
            <input type='hidden' name='sektion' value='person' />
            <input type='hidden' name='form' value='true' />
            <input type='hidden' name='pid' value="{$dialog['id'][1]}" />
        </form>
    </td>
</tr></table>   {* Ende Kopfzeile *}

<table style='margin-left:30px'>
    <colgroup><col width='10%'><col><col></colgroup>
        <tr>
            <td class='re'>
                {if isset($dialog['gtag'])}
                    {$dialog['gtag'][2]}:
            </td>
            <td>
                {$dialog['gtag'][1]}
                {/if}
                {if isset($dialog['gort'])}
                    &nbsp;{$dialog['gort'][2]}&nbsp;{$dialog['gort'][1]['ort']}
                    &nbsp;({$dialog['gort'][1]['land']},&nbsp;{$dialog['gort'][1]['bland']})
                {/if}
            </td>
            <td rowspan='5'>{*<img src='bild.png' width='100' height='135' alt='bild' />*}</td>
        </tr>

        <tr>
            <td class='re'>
                {if isset($dialog['ttag'])}
                    {if ($dialog['ttag'][1]) OR (isset($dialog['tort']) AND $dialog['tort'][1])} {$dialog['ttag'][2]}:
            </td>
            <td>
                    {$dialog['ttag'][1]}
                {/if}{/if}
                {if isset($dialog['tort']) AND $dialog['tort'][1]}
                    &nbsp;{$dialog['tort'][2]}&nbsp;{$dialog['tort'][1]['ort']}
                    &nbsp;({$dialog['tort'][1]['land']},&nbsp;{$dialog['tort'][1]['bland']})
                {/if}
            </td>
        </tr>

        <tr>
            <td class='re'>{if isset($dialog['strasse']) AND $dialog['strasse'][1]}
                {$dialog['strasse'][2]}:
            </td>
            <td>
                {$dialog['strasse'][1]}<br />{/if}
                {if isset($dialog['plz'])}{$dialog['plz'][1]}&nbsp;{/if}
                {if isset($dialog['wort'])}
                {$dialog['wort'][1]['ort']}&nbsp;({$dialog['wort'][1]['land']}&nbsp;-&nbsp;{$dialog['wort'][1]['bland']}){/if}
            </td>
        </tr>

{if isset($dialog['tel'])}
        <tr>
            <td class='re'>{$dialog['tel'][2]}:</td>
            <td>{$dialog['tel'][1]}</td>
        </tr>
{/if}

{if isset($dialog['mail'])}
        <tr>
            <td class='re'>{$dialog['mail'][2]}:</td>
            <td>{$dialog['mail'][1]}</td>
        </tr>
{/if}

{if isset($dialog['biogr'])}
        <tr>
            <td class='re'>{$dialog['biogr'][2]}:</td>
            <td colspan='2' style='white-space:normal'>{$dialog['biogr'][1]|nl2br}</td>
        </tr>
{/if}

{if isset($dialog['notiz'])}
        <tr>
            <td class='re'>{$dialog['notiz'][2]}:</td>
            <td colspan='2' style='white-space:normal'>{$dialog['notiz'][1]|nl2br}</td>
        </tr>
{/if}
</table>
