{**************************************************************
Smarty-Template für die Ansicht von Personendaten

$Rev$
$Author$
$Date$
$URL$

    call:   pers_class.php
    class:  Person
    proc:   view
    param:  dialog[???][0] feldname
                       [1] inhalt (evt. weitere arrays)
                       [2] label
                       [3] Tooltip (soweit vorhanden)

***** (c) DIAF e.V. *******************************************}

<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="100px"><col><col></colgroup>

{* --Name-- *}
    <tr>
        <td colspan="2">
            <div style="white-space:normal" class="fett">
            {if !empty($dialog['vname'][1])}{$dialog['vname'][1]}&nbsp;{/if}
            {if !empty($dialog['name'][1])} {$dialog['name'][1]}{/if}
            {if !empty($dialog['aliases'][1])}
                <span style="font-weight:normal">&nbsp;
                ({foreach from=$dialog['aliases'][1] item=alias}{$alias}{/foreach})</span>
            {/if}
            </div>
        </td>

{* --Bearbeitungssymbole-- *}
        <td style="text-align:right;">
            <form action='{$dlg['phpself']}' method="post">
            <span class="note">
                ID:&nbsp;{$dialog['id'][1]}&nbsp;
                {if isset($dialog['chname'])}|&nbsp;{$dialog['chname'][1]}&nbsp;{/if}
                {if isset($dialog['chdatum'])}|&nbsp;{$dialog['chdatum'][1]}&nbsp;{/if}
            </span>
                {if isset($dialog['edit'])}
                    <button
                        class={if $darkBG}"small_dk"{else}"small"{/if}
                        name="aktion"
                        onmouseover="return overlib('{$dialog['edit'][3]}',DELAY,1000);"
                        onmouseout="return nd();"
                        value="edit"><img src="images/edit.png" /></button>
                {/if}
                {if isset($dialog['del'])}
                    <button
                        class={if $darkBG}"small_dk"{else}"small"{/if}
                        name="aktion"
                        onmouseover="return overlib('{$dialog['del'][3]}',DELAY,1000);"
                        onmouseout="return nd();"
                        value="del" /><img src="images/del.png" /></button>
                {/if}
                <input type="hidden" name="form" value="true" />
                <input type="hidden" name="id" value="{$dialog['id'][1]}" />
            </form>
        </td>
    </tr>

{* --Geburtstagszeile-- *}
    {if !empty($dialog['gtag'][1]) OR !empty($dialog['gort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['gtag'][2])}{$dialog['gtag'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['gtag'][1])}{$dialog['gtag'][1]}{/if}
            {if !empty($dialog['gort'][1])}
                &nbsp;{$dialog['gort'][2]}&nbsp;{$dialog['gort'][1]['ort']}
                &nbsp;({$dialog['gort'][1]['land']},&nbsp;{$dialog['gort'][1]['bland']})
            {/if}
        </td>
        <td >{*<img src="images/platzhalter.png" width="100" height="135" alt="bild" />*}</td>
    </tr>{/if}

{* --Todeszeile-- *}
    {if !empty($dialog['ttag'][1]) OR !empty($dialog['tort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['ttag'][1])}{$dialog['ttag'][2]}{/if}
        </td>
        <td>{if !empty($dialog['ttag'][1])}{$dialog['ttag'][1]}{/if}
            {if !empty($dialog['tort'][1])}
                &nbsp;{$dialog['tort'][2]}&nbsp;{$dialog['tort'][1]['ort']}
                &nbsp;({$dialog['tort'][1]['land']},&nbsp;{$dialog['tort'][1]['bland']})
            {/if}
        </td>
    </tr>{/if}

{* --Anschrift-- *}
    {if !empty($dialog['strasse'][1]) OR !empty($dialog['plz'][1]) OR !empty($dialog['wort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['strasse'][2])}{$dialog['strasse'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['strasse'][1])}{$dialog['strasse'][1]}<br />{/if}
            {if !empty($dialog['plz'])}{$dialog['plz'][1]}&nbsp;{/if}
            {if !empty($dialog['wort'][1])}
                {$dialog['wort'][1]['ort']}&nbsp;({$dialog['wort'][1]['land']},&nbsp;{$dialog['wort'][1]['bland']})
            {/if}
        </td>
    </tr>{/if}

{* --Telefonzeile-- *}
    {if !empty($dialog['tel'][1])}<tr>
        <td class="re">{$dialog['tel'][2]}:</td>
        <td>{$dialog['tel'][1]}</td>
    </tr>{/if}

{* --Mailzeile-- *}
    {if !empty($dialog['mail'][1])}<tr>
            <td class="re">{$dialog['mail'][2]}:</td>
            <td><a href="mailto:{$dialog['mail'][1]}">{$dialog['mail'][1]}</a></td>
    </tr>{/if}


{* --Biografiezeile-- *}
    {if !empty($dialog['biogr'][1])}<tr>
            <td class="re" style="vertical-align:top">{$dialog['biogr'][2]}:</td>
            <td colspan="2" style="white-space:normal">{$dialog['biogr'][1]|nl2br}</td>
    </tr>{/if}

{* --Verweis auf Filmografie-- *}
    {if !empty($dialog['castLi'][1])}
        {foreach from=$dialog['castLi'][1] item=cast}<tr>
            <td><!-- Label --></td>
            <td>{$cast['ftitel']}</td>
            <td>{$cast['job']}</td>
        </tr>{/foreach}
    {/if}

{* --Notizfeld-- *}
    {if !empty($dialog['notiz'][1])}<tr>
            <td class="re" style="vertical-align:top">{$dialog['notiz'][2]}:</td>
            <td colspan="2" style="white-space:normal">{$dialog['notiz'][1]|nl2br}</td>
    </tr>{/if}
</table>
