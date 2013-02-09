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
    <tr><form method="post">
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
</table>