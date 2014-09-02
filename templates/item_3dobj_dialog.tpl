{*****************************************************************************

 Edittemplate für alle räumlichen Gegenstände

 call:   class.item.php
 class:  Obj3d
 proc:   edit
 param: dialog[???][0] feldname
                   [1] inhalt (evt. weitere arrays)
                   [2] label
                   [3] Tooltip (soweit vorhanden)

 $Rev: 50 $
 $Author: knwetzig $
 $Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
 $URL: https://diafip.googlecode.com/svn/trunk/templates/item_3dobj_dialog.tpl $

 ***** (c) DIAF e.V. *********************************************************}

<form action='{$dlg['phpself']}' method='post'>

<div name='links' style='float:left'>
<table>
    <colgroup><col><col></colgroup>

{* --- bezeichner --- *}
    {if isset($dialog['bezeichner'])}<tr>
        <td class="re">{$dialog['bezeichner'][2]}:</td>
        <td><input type='text' name='bezeichner' value="{$dialog['bezeichner'][1]}" /></td>
    </tr>{/if}

{* --- Filmzuordnung --- *}
    {if isset($dialog['zu_film'])}<tr>
        <td class="re">{$dialog['zu_film'][2]}:</td>
        <td>
            {html_options
                name=$dialog['zu_film'][0]
                options=$dialog['filmLi'][1]
                selected=$dialog['zu_film'][1]
            }
        </td>
    </tr>{/if}

{* --- Breite --- *}
    {if isset($dialog['x'])}<tr>
        <td class="re">{$dialog['x'][2]}:</td>
        <td><input type='text' name='x' value="{$dialog['x'][1]}" />&nbsp;mm</td>
    </tr>{/if}

{* --- Höhe --- *}
    {if isset($dialog['y'])}<tr>
        <td class="re">{$dialog['y'][2]}:</td>
        <td><input type='text' name='y' value="{$dialog['y'][1]}" />&nbsp;mm</td>
    </tr>{/if}

{* --- Tiefe --- *}
    {if isset($dialog['z'])}<tr>
        <td class="re">{$dialog['z'][2]}:</td>
        <td><input type='text' name='z' value="{$dialog['z'][1]}" />&nbsp;mm</td>
    </tr>{/if}

{* --- Lagerort --- *}
    {if isset($dialog['lagerort'])}<tr>
        <td class="re">{$dialog['lagerort'][2]}:</td>
        <td>
            {html_options
                name=$dialog['lagerort'][0]
                options=$dialog['lortLi'][1]
                selected=$dialog['lagerort'][1]
            }
        </td>
    </tr>{/if}

{* --- akt_ort --- *}
    {if isset($dialog['akt_ort'])}<tr>
        <td class="re">{$dialog['akt_ort'][2]}:</td>
        <td><input type='text' name='akt_ort' value="{$dialog['akt_ort'][1]}" /></td>
    </tr>{/if}

{* --- Stückzahl --- *}
    {if isset($dialog['kollo'])}<tr>
        <td class="re">{$dialog['kollo'][2]}:</td>
        <td><input type='text' name='kollo' value="{$dialog['kollo'][1]}" /></td>
    </tr>{/if}

{* --- Wert zum Zeitpunkt der Anschaffung --- *}
    {if isset($dialog['a_wert'])}<tr>
        <td class="re">{$dialog['a_wert'][2]}:</td>
        <td><input type='text' name='a_wert' value="{$dialog['a_wert'][1]}" />&euro;</td>
    </tr>{/if}

{* --- Eigentümer --- *}
    {if isset($dialog['eigner'])}<tr>
        <td class="re">{$dialog['eigner'][2]}:</td>
        <td>
            {html_options
                name=$dialog['eigner'][0]
                options=$dialog['persLi'][1]
                selected=$dialog['eigner'][1]
            }
        </td>
    </tr>{/if}

{* --- herkunft --- *}
    {if isset($dialog['herkunft'])}<tr>
        <td class="re">{$dialog['herkunft'][2]}:</td>
        <td>
            {html_options
                name=$dialog['herkunft'][0]
                options=$dialog['persLi'][1]
                selected=$dialog['herkunft'][1]
            }
        </td>
    </tr>{/if}

{* --- Zugangsdatum --- *}
    {if isset($dialog['in_date'])}<tr>
        <td class="re">{$dialog['in_date'][2]}:</td>
        <td><input type='text' name='in_date' value="{$dialog['in_date'][1]}" /></td>
    </tr>{/if}

{* --- Beschreibung --- *}
    {if isset($dialog['descr'])}<tr>
        <td class="re top">{$dialog['descr'][2]}:</td>
        <td><textarea class='medium' name='descr'>{$dialog['descr'][1]}</textarea></td>
    </tr>{/if}


  </table></div>

<div name='rechts' style='float:right'>
<table>
{* --- Gegenstandstyp --- *}
    {if !empty($dialog['art'])}<tr><td colspan='2'>
        <fieldset><legend>{$dialog['art'][2]}</legend>
            {html_radios name=$dialog['art'][0] options=$dialog['artLi'][1]    selected=$dialog['art'][1] separator='<br />'}
        </fieldset>
    </td></tr>{/if}

{* --- Restaurierungsbericht --- *}
    {if isset($dialog['rest_report'])}<tr><td colspan='2'>
            {$dialog['rest_report'][2]}:<br />
            <textarea class='small' name='rest_report'>{$dialog['rest_report'][1]}</textarea>
    </td></tr>{/if}

{* --- leihbar --- *}
    {if isset($dialog['leihbar'])}<tr>
        <td class='re'>{$dialog['leihbar'][2]}:&nbsp;</td>
        <td><input
            type='checkbox'
            name='leihbar'
            {if $dialog['leihbar'][1]}checked="checked"{/if}
            value='true' />
        </td>
    </tr>{/if}

{* --- Alte Signatur --- *}
    {if isset($dialog['oldsig'])}<tr>
        <td class='re'>{$dialog['oldsig'][2]}:&nbsp;</td>
        <td><input class='halb' type='text' name='oldsig' value="{$dialog['oldsig'][1]}" /></td>
    </tr>{/if}

{* --- notiz --- *}
    {if isset($dialog['notiz'])}<tr>
        <td colspan='2'>
            {$dialog['notiz'][2]}:&nbsp;<br />
            <textarea class='small' name='notiz'>{$dialog['notiz'][1]}</textarea>
        </td>
    </tr>{/if}

{* --- isvalid --- *}
    {if isset($dialog['isvalid'])}<tr>
        <td colspan='2'>
            <input
                type="checkbox"
                name="isvalid"
                {if $dialog['isvalid'][1]}
                    checked="checked"
                {/if}
                value="true" /> {$dialog['isvalid'][2]}
        </td>
    </tr>{/if}

    <tr><td></td>
        <td class="re">
        <input type='submit' name='submit' />
        <input type='hidden' name='sektion' value='i_3dobj' />
        <input type='hidden' name='aktion' value='{$aktion}' />
    </tr>
</table>
</div>

<!-- Absendebutton und Kramzeug -->

</form>
<div style='clear:both'>&nbsp;</div>