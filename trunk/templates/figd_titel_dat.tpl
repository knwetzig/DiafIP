{**************************************************************

Smarty-Template für die Einzelansicht von Titeldaten

call: figd_class.php
class: Titel
proc:  view
param:	dialog[???][0] feldname
                    [1] inhalt (evt. weitere arrays)
                    [2] label
                    [3] Tooltip (soweit vorhanden)

$Rev$
$Author$
$Date: 2012-07-31 22:11:39 +0#$
$URL$

***** (c) DIAF e.V. *******************************************}


<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="100px"><col><col width="150px"></colgroup>

{* --Name-- *}
    <tr>
        <td colspan="2">
            {if !empty($dialog['titel'][1])}<div style="white-space:normal" class="fett">{$dialog['titel'][1]}</div>{/if}
        </td>

        {* --Bearbeitungssymbole-- *}
        <td style="text-align:right;">
            <form method="post">
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
                <input type="hidden" name="sektion" value="titel" />
                <input type="hidden" name="form" value="true" />
                <input type="hidden" name="tid" value="{$dialog['id'][1]}" />
            </form>
        </td>
    </tr>

{* --Untertitel-- *}
    {if !empty($dialog['utitel'][1])}<tr>
        <td class="re">
            {if !empty($dialog['utitel'][2])}{$dialog['utitel'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['utitel'][1])}{$dialog['utitel'][1]}{/if}
        </td>
        <td rowspan="5"><img src="bild.png" width="100" height="135" alt="bild" /></td>
    </tr>{/if}

{* --Arbeitstitel-- *}
    {if !empty($dialog['atitel'][1])}<tr>
        <td class="re">{$dialog['atitel'][2]}:</td>
        <td>{$dialog['atitel'][1]}</td>
    </tr>{/if}

{* --Serientitel-- *}
    {if !empty($dialog['stitel'][1])}<tr>
            <td class="re">{$dialog['stitel'][2]}:</td>
            <td {if !empty($dialog['sdescr'][1])}onmouseover="return overlib('{$dialog['sdescr'][1]}',DELAY,500);"
              onmouseout="return nd();"{/if}>{$dialog['stitel'][1]} {if !empty($dialog['sfolge'][1])}({$dialog['sfolge'][1]}){/if}</td>
    </tr>{/if}
</table>