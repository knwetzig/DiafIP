{**************************************************************

Smarty-Template für die Listenansicht von filmogr. Daten

call: figd_class.php
class: Film
proc:  sview
param: dialog[???][0] feldname
                    [1] inhalt (evt. weitere arrays)
                    [2] label
                    [3] Tooltip (soweit vorhanden)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="200px"><col><col width="200px"></colgroup>

{* --- Name/Status/Bearbeitungssymbole --- *}
    <tr><form action='{$dlg[10]}' method="post">
        <td colspan="3">
            {if !empty($dialog['titel'][1])}<span class="fett">{$dialog['titel'][1]}</span>{/if}
        <span class="note" style="float:right;">
                ID:&nbsp;{$dialog['id'][1]}&nbsp;

                <button
                    class={if $darkBG}"small_dk"{else}"small"{/if}
                    name="aktion"
                    value="view"><img src="images/view_detailed.png" />
                </button>

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
        </span></td>
    </form></tr>

{* --Regie-- *}
    {if !empty($dialog['regie'][1])}<tr>
        <td class="re">{$dialog['regie'][2]}:</td>
        <td>{$dialog['regie'][1]}</td>
    </tr>{/if}

{* --prod_jahr-- *}
    {if !empty($dialog['prod_jahr'][1])}<tr>
        <td class="re">{$dialog['prod_jahr'][2]}:</td>
        <td>{$dialog['prod_jahr'][1]}</td>
    </tr>{/if}

{* --prodtech-- *}
    {if !empty($dialog['prodtech'][1])}<tr>
        <td class="re" style="vertical-align:top">{$dialog['prodtech'][2]}:</td>
        <td>{foreach from=$dialog['prodtech'][1] item=wert}{$wert}<br />{/foreach}</td>
    </tr>{/if}

</table>