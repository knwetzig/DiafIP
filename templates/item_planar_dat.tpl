 {*****************************************************************************

 Smarty-Template für Dokumente/Phasen Urkunden und alle planaren Gegenstände

 call:   class.item.php
 class:  Planar
 proc:   view
 param:	dialog[???][0] feldname
                   [1] inhalt (evt. weitere arrays)
                   [2] label
                   [3] Tooltip (soweit vorhanden)

 $Rev$
 $Author$
 $Date$
 $URL$

 ***** (c) DIAF e.V. *********************************************************}


<table width="100%" {if $darkBG} style="background-image:url(images/bg_dark.png)"{/if}>
    <colgroup><col width="200px"><col><col width="200px"></colgroup>

{* --- Name/Status/Bearbeitungssymbole --- *}
    <tr><form method="post">
        <td colspan="3">
            {if !empty($dialog['bezeichner'][1])}<span class="fett">{$dialog['bezeichner'][1]}</span>{/if}
        <span class="note" style="float:right;">
                ID:&nbsp;{$dialog['id'][1]}&nbsp;|&nbsp;{$dialog['chdatum'][1]}&nbsp;{if !empty($dialog['chname'][1])}|&nbsp;{$dialog['chname'][1]}&nbsp;{/if}
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
                <input type="hidden" name="sektion" value="i_planar" />
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


{* -- art -- *}
    {if !empty($dialog['art'][1])}<tr>
        <td class="re">
            {if !empty($dialog['art'][2])}{$dialog['art'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['art'][1])}{$dialog['art'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Breite -- *}
    {if !empty($dialog['x'][1])}<tr>
        <td class="re">
            {if !empty($dialog['x'][2])}{$dialog['x'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['x'][1])}{$dialog['x'][1]}&nbsp;mm{/if}
        </td>
    </tr>{/if}

{* -- Höhe -- *}
    {if !empty($dialog['y'][1])}<tr>
        <td class="re">
            {if !empty($dialog['y'][2])}{$dialog['y'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['y'][1])}{$dialog['y'][1]}&nbsp;mm{/if}
        </td>
    </tr>{/if}

{* -- lagerort -- *}
    {if !empty($dialog['lagerort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['lagerort'][2])}{$dialog['lagerort'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['lagerort'][1])}{$dialog['lagerort'][1]}{/if}
        </td>
    </tr>{/if}

{* -- akt_ort -- *}
    {if !empty($dialog['akt_ort'][1])}<tr>
        <td class="re">
            {if !empty($dialog['akt_ort'][2])}{$dialog['akt_ort'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['akt_ort'][1])}{$dialog['akt_ort'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Stückzahl -- *}
    {if !empty($dialog['kollo'][1])}<tr>
        <td class="re">
            {if !empty($dialog['kollo'][2])}{$dialog['kollo'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['kollo'][1])}{$dialog['kollo'][1]}{/if}
        </td>
    </tr>{/if}

{* -- leihbar -- *}
    {if !empty($dialog['leihbar'][1])}<tr>
        <td class="re">
            {if !empty($dialog['leihbar'][2])}{$dialog['leihbar'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['leihbar'][1])}{$dialog['leihbar'][1]}{/if}
        </td>
    </tr>{/if}

{* -- vers_wert -- *}
    {if !empty($dialog['vers_wert'][1])}<tr>
        <td class="re">
            {if !empty($dialog['vers_wert'][2])}{$dialog['vers_wert'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['vers_wert'][1])}{$dialog['vers_wert'][1]}&nbsp;&euro;{/if}
        </td>
    </tr>{/if}

{* -- Eigentümer -- *}
    {if !empty($dialog['eigner'][1])}<tr>
        <td class="re">
            {if !empty($dialog['eigner'][2])}{$dialog['eigner'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['eigner'][1])}{$dialog['eigner'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Herkunft -- *}
    {if !empty($dialog['herkunft'][1])}<tr>
        <td class="re">
            {if !empty($dialog['herkunft'][2])}{$dialog['herkunft'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['herkunft'][1])}{$dialog['herkunft'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Eingangsdatum -- *}
    {if !empty($dialog['in_date'][1])}<tr>
        <td class="re">
            {if !empty($dialog['in_date'][2])}{$dialog['in_date'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['in_date'][1])}{$dialog['in_date'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Beschreibung -- *}
    {if !empty($dialog['descr'][1])}<tr>
        <td class="re top">
            {if !empty($dialog['descr'][2])}{$dialog['descr'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['descr'][1])}{$dialog['descr'][1]}{/if}
        </td>
    </tr>{/if}

{* -- Zustandsbericht -- *}
    {if !empty($dialog['rest_report'][1])}<tr>
        <td class="re top">
            {if !empty($dialog['rest_report'][2])}{$dialog['rest_report'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['rest_report'][1])}{$dialog['rest_report'][1]}{/if}
        </td>
    </tr>{/if}

{* -- oldsig -- *}
    {if !empty($dialog['oldsig'][1])}<tr class='note'>
        <td class="re">
            {if !empty($dialog['oldsig'][2])}{$dialog['oldsig'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['oldsig'][1])}{$dialog['oldsig'][1]}{/if}
        </td>
    </tr>{/if}

{* -- notiz -- *}
    {if !empty($dialog['notiz'][1])}<tr class='note'>
        <td class="re top">
            {if !empty($dialog['notiz'][2])}{$dialog['notiz'][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog['notiz'][1])}{$dialog['notiz'][1]}{/if}
        </td>
    </tr>{/if}

{* --  -- *}
    {if !empty($dialog[''][1])}<tr>
        <td class="re">
            {if !empty($dialog[''][2])}{$dialog[''][2]}:{/if}
        </td>
        <td>
            {if !empty($dialog[''][1])}{$dialog[''][1]}{/if}
        </td>
    </tr>{/if}


</table>