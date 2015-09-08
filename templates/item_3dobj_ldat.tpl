 {*****************************************************************************

 Smarty-Template für alle räumlichen Gegenstände (Detailansicht)

 call:   item.class.php
 class:  Obj3d
 proc:   sview
 param:	dialog[???][0] feldname
                   [1] inhalt (evt. weitere arrays)
                   [2] label
                   [3] Tooltip (soweit vorhanden)

 $Rev: 50 $
 $Author: knwetzig $
 $Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
 $URL: https://diafip.googlecode.com/svn/trunk/templates/item_3dobj_ldat.tpl $

 ***** (c) DIAF e.V. *********************************************************}

<div class="list-item list-item-3dobj">
<table width="100%" {if $darkBG}class="even"{else}class="odd"{/if}>
    <colgroup><col width="200px"><col><col width="200px"></colgroup>

{* --- Name/Status/Bearbeitungssymbole --- *}
    <tr><form action='{$dlg['phpself']}' method="post">
        <td colspan="3">
            {if !empty($dialog['bezeichner'][1])}
            <h3>{$dialog['bezeichner'][1]}</h3>{/if}
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

{* -- art -- *}
    {if !empty($dialog['art'][1])}<tr>
        <td class="re">
            {if !empty($dialog['art'][2])}{$dialog['art'][2]}:{/if}
        </td>
        <td>{$dialog['art'][1]}</td>
    </tr>{/if}

{* -- Zuordnung zu Film -- *}
    {if !empty($dialog['zu_film'][1])}<tr>
        <td class="re">
            {if !empty($dialog['zu_film'][2])}{$dialog['zu_film'][2]}:{/if}
        <td>{$dialog['zu_film'][1]}<td>
    </tr>{/if}

{* -- Maße -- *}
    {if !empty($dialog['masze'][1])}<tr>
        <td class="re">
            {if !empty($dialog['masze'][2])}{$dialog['masze'][2]}:{/if}
        </td>
        <td>{$dialog['masze'][1]}&nbsp;mm</td>
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
</div>