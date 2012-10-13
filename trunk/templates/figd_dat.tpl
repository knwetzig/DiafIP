{**************************************************************

Smarty-Template für die Detailansicht von filmogr. Daten

call: figd_class.php
class: Film
proc:  view
param:  dialog[???][0] feldname
                    [1] inhalt (evt. weitere arrays)
                    [2] label
                    [3] Tooltip (soweit vorhanden)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="100px"><col><col></colgroup>

{* --Name-- *}
    <tr>
        <td colspan="2">
            {if !empty($dialog['titel'][1])}<div style="white-space:nowrap" class="fett">{$dialog['titel'][1]}</div>{/if}
        </td>

        {* --Bearbeitungssymbole-- *}
        <td style="text-align:right;">
            <form method="post">
            <span class="note">
                FID:&nbsp;{$dialog['id'][1]}&nbsp;|&nbsp;{$dialog['chdatum'][1]}&nbsp;|&nbsp;{$dialog['chname'][1]}&nbsp;
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
                <input type="hidden" name="sektion" value="film" />
                <input type="hidden" name="form" value="true" />
                <input type="hidden" name="fid" value="{$dialog['id'][1]}" />
            </form>
        </td>
    </tr>
{* --Bild-- *}
    {if !empty($dialog['bild_id'][1])}<tr><td colspan="2"></td>
        <td rowspan="7">
            <img src="images/platzhalter.png" width="200" height="150" alt="bild" />
        </td>
    <tr>{/if}


{* --Untertitel-- *}
    {if !empty($dialog['utitel'][1])}<tr>
        <td class="re">
            {if !empty($dialog['utitel'][2])}{$dialog['utitel'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['utitel'][1])}{$dialog['utitel'][1]}{/if}
        </td>
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

{* --prod_jahr-- *}
    {if !empty($dialog['prod_jahr'][1])}<tr>
        <td class="re">{$dialog['prod_jahr'][2]}:</td>
        <td>{$dialog['prod_jahr'][1]}</td>
    </tr>{/if}

{* --thema-- *}
    {if !empty($dialog['thema'][1])}<tr>
        <td class="re">{$dialog['thema'][2]}:</td>
        <td>{$dialog['thema'][1]}</td>
    </tr>{/if}

{* --gattung-- *}
    {if !empty($dialog['gattung'][1])}<tr>
        <td class="re">{$dialog['gattung'][2]}:</td>
        <td>{$dialog['gattung'][1]}</td>
    </tr>{/if}

{* --prodtech-- *}
    {if !empty($dialog['prodtech'][1])}<tr>
        <td class="re" style="vertical-align:top">{$dialog['prodtech'][2]}:</td>
        <td>{foreach from=$dialog['prodtech'][1] item=wert}{$wert}<br />{/foreach}</td>
    </tr>{/if}

{* --laenge-- *}
    {if !empty($dialog['laenge'][1])}<tr>
        <td class="re">{$dialog['laenge'][2]}:</td>
        <td>{$dialog['laenge'][1]}</td>
    </tr>{/if}

{* --fsk-- *}
    {if !empty($dialog['fsk'][1])}<tr>
        <td class="re">{$dialog['fsk'][2]}:</td>
        <td>{$dialog['fsk'][1]}</td>
    </tr>{/if}

{* --praedikat-- *}
    {if !empty($dialog['praedikat'][1])}<tr>
        <td class="re">{$dialog['praedikat'][2]}:</td>
        <td>{$dialog['praedikat'][1]}</td>
    </tr>{/if}

{* --urrauff-- *}
    {if !empty($dialog['urrauff'][1])}<tr>
        <td class="re">{$dialog['urrauff'][2]}:</td>
        <td>{$dialog['urrauff'][1]}</td>
    </tr>{/if}

{* --mediaspezi-- *}
    {if !empty($dialog['mediaspezi'][1])}<tr>
        <td class="re" style="vertical-align:top">{$dialog['mediaspezi'][2]}:</td>
        <td>{foreach from=$dialog['mediaspezi'][1] item=wert}{$wert}<br />{/foreach}</td>
    </tr>{/if}

{* --Besetzung-- *}
    {if !empty($dialog['cast'][1])}
    {foreach from=$dialog['cast'][1] item=cast}<tr>
        <td class="re">{$cast['job']}:</td>
        <td>{$cast['vname']}&nbsp;{$cast['name']}</td>
    </tr>{/foreach}
    {/if}

{* --inhalt-- *}
    {if !empty($dialog['inhalt'][1])}<tr>
        <td class="re" style="vertical-align:top">{$dialog['inhalt'][2]}:</td>
        <td colspan="2">{$dialog['inhalt'][1]|nl2br}</td>
    </tr>{/if}

{* --quellen-- *}
    {if !empty($dialog['quellen'][1])}<tr>
        <td class="re">{$dialog['quellen'][2]}:</td>
        <td colspan="2">{$dialog['quellen'][1]}</td>
    </tr>{/if}

{* --anmerk-- *}
    {if !empty($dialog['anmerk'][1])}<tr>
        <td class="re" style="vertical-align:top">{$dialog['anmerk'][2]}:</td>
        <td colspan="2">{$dialog['anmerk'][1]|nl2br}</td>
    </tr>{/if}

{* --notiz-- letzter Eintrag *}
    {if !empty($dialog['notiz'][1])}<tr class="note">
        <td class="re" style="vertical-align:top">{$dialog['notiz'][2]}:</td>
        <td colspan="2">{$dialog['notiz'][1]|nl2br}</td>
    </tr>{/if}

{* --isvalid-- Eintrag *}
    {if !empty($dialog['isVal'][1])}<tr>
        <td>&nbsp;</td>
        <td colspan="2" class="re"><img src="images/ok.png" />&nbsp;{$dialog['isVal'][2]}</td>
    </tr>{/if}

</table>