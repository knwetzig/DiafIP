 {*****************************************************************************

 Smarty-Template für Filmkopien (Detailansicht)

 call:   class.item.php
 class:  FilmKopie
 proc:   view
 param:	dialog[???][0] feldname
                   [1] inhalt (evt. weitere arrays)
                   [2] label
                   [3] Tooltip (soweit vorhanden)

 $Rev: 50 $
 $Author: knwetzig $
 $Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
 $URL: https://diafip.googlecode.com/svn/trunk/templates/item_fkop_ldat.tpl $

 ***** (c) DIAF e.V. *********************************************************}


<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="200px"><col><col width="200px"></colgroup>

{* --- Name/Status/Bearbeitungssymbole --- *}
    <tr><form action='{$dlg['phpself']}' method="post">
        <td colspan="3">
            {if !empty($dialog['bezeichner'][1])}
            <span class="fett" style="float:left;">{$dialog['bezeichner'][1]}</span>{/if}
        <span class="note" style="float:right;">
                ID:&nbsp;{$dialog['id'][1]}

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

{* -- Bild -- *}
    {if !empty($dialog['bild_id'][1])}<tr><td colspan="2"></td>
        <td rowspan="7">
            <img src="images/platzhalter.png" width="200" height="150" alt="bild" />
        </td>
    <tr>{/if}

{* -- Zuordnung zu Film -- *}
    {if !empty($dialog['zu_film'][1])}<tr>
        <td class="re">
            {if !empty($dialog['zu_film'][2])}{$dialog['zu_film'][2]}:{/if}
        <td>{$dialog['zu_film'][1]}<td>
    </tr>{/if}

{* -- medium -- *}
    {if !empty($dialog['medium'][1])}<tr>
        <td class="re">
            {if !empty($dialog['medium'][2])}{$dialog['medium'][2]}:{/if}
        </td>
        <td>{$dialog['medium'][1]}</td>
    </tr>{/if}

{* -- laufzeit -- *}
    {if !empty($dialog['laufzeit'][1])}<tr>
        <td class="re">
            {if !empty($dialog['laufzeit'][2])}{$dialog['laufzeit'][2]}:{/if}
        </td>
        <td>{$dialog['laufzeit'][1]}</td>
    </tr>{/if}

{* -- lagerort -- *}
    {if !empty($dialog['lagerort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['lagerort'][2])}{$dialog['lagerort'][2]}:{/if}
        </td>
        <td>{$dialog['lagerort'][1]}</td>
    </tr>{/if}

{* -- akt_ort -- *}
    {if !empty($dialog['akt_ort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['akt_ort'][2])}{$dialog['akt_ort'][2]}:{/if}
        </td>
        <td>{$dialog['akt_ort'][1]}</td>
    </tr>{/if}

</table>