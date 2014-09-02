{*****************************************************************************

 Edittemplate für alle Filmkopien

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
 $URL: https://diafip.googlecode.com/svn/trunk/templates/item_fkop_dialog.tpl $

 ***** (c) DIAF e.V. *********************************************************}

<form action='{$dlg['phpself']}' method='post'>

<div name='links' style='float:left'>
<table>
    <colgroup><col><col></colgroup>

{* --- bezeichner --- *}
    {if isset($dialog['bezeichner'])}<tr>
        <td class="re"><label for='bezeich'>{$dialog['bezeichner'][2]}:</label></td>
        <td><input type='text' id='bezeich' name='bezeichner' value="{$dialog['bezeichner'][1]}" /></td>
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

{* --- Trägermaterial --- *}
    {if isset($dialog['material'])}<tr>
        <td class="re"><label for='material'>{$dialog['material'][2]}:</label></td>
        <td>
            {html_options
                id='material'
                name=$dialog['material'][0]
                options=$dialog['materialLi'][1]
                selected=$dialog['material'][1]
            }
        </td>
    </tr>{/if}

{* --- tonart --- *}
    {if isset($dialog['tonart'])}<tr>
        <td class="re">{$dialog['tonart'][2]}:</td>
        <td>
            {html_options
                name=$dialog['tonart'][0]
                options=$dialog['tonartLi'][1]
                selected=$dialog['tonart'][1]
            }
        </td>
    </tr>{/if}

{* --- bilder/s --- *}
    {if isset($dialog['fps'])}<tr>
        <td class="re"><label for='fps'>{$dialog['fps'][2]}:</label></td>
        <td><input type='text' class='halb' id='fps' name='fps' value="{$dialog['fps'][1]}" /></td>
    </tr>{/if}

{* --- Laufzeit --- *}
    {if isset($dialog['lzeit'])}<tr>
        <td class="re"><label for='lzeit'>{$dialog['lzeit'][2]}:</label></td>
        <td><input
            type='text'
            class='halb'
            id='lzeit'
            name='lzeit'
            value="{$dialog['lzeit'][1]}"
            onmouseover="return overlib('{$dialog['lzeit'][3]}',DELAY,1000,WIDTH,600);"
            onmouseout="return nd();"
        /></td>
    </tr>{/if}


{* --- Lagerort --- *}
    {if isset($dialog['lagerort'])}<tr>
        <td class="re">{$dialog['lagerort'][2]}:</td>
        <td>{html_options
                name=$dialog['lagerort'][0]
                options=$dialog['lortLi'][1]
                selected=$dialog['lagerort'][1]
            }
        </td>
    </tr>{/if}

{* --- akt_ort --- *}
    {if isset($dialog['akt_ort'])}<tr>
        <td class="re"><label for='alort'>{$dialog['akt_ort'][2]}:</label></td>
        <td><input type='text' id='alort' name='akt_ort' value="{$dialog['akt_ort'][1]}" /></td>
    </tr>{/if}

{* --- Stückzahl --- *}
    {if isset($dialog['kollo'])}<tr>
        <td class="re"><label for='kollo'>{$dialog['kollo'][2]}:</label></td>
        <td><input type='text' id='kollo' name='kollo' value="{$dialog['kollo'][1]}" /></td>
    </tr>{/if}

{* --- leihbar --- *}
    {if isset($dialog['leihbar'])}<tr>
        <td class='re'><label for='verleih'>{$dialog['leihbar'][2]}:&nbsp;</label></td>
        <td><input
            type='checkbox'
            id='verleih'
            name='leihbar'
            {if $dialog['leihbar'][1]}checked="checked"{/if}
            value='true'
        /></td>
    </tr>{/if}

{* --- Wert zum Zeitpunkt der Anschaffung --- *}
    {if isset($dialog['a_wert'])}<tr>
        <td class="re"><label for='awert'>{$dialog['a_wert'][2]}:</label></td>
        <td><input type='text' id='awert' name='a_wert' value="{$dialog['a_wert'][1]}" />&euro;</td>
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
        <td class="re"><label for='edat'>{$dialog['in_date'][2]}:</label></td>
        <td><input type='text' id='edat' name='in_date' value="{$dialog['in_date'][1]}" /></td>
    </tr>{/if}

{* --- Beschreibung --- *}
    {if isset($dialog['descr'])}<tr>
        <td class="re top"><label for='descr'>{$dialog['descr'][2]}:</label></td>
        <td><textarea class='medium' id='descr' name='descr'>{$dialog['descr'][1]}</textarea></td>
    </tr>{/if}


  </table></div>

<div name='rechts' style='float:right'>
<table>

{* --- Medienauswahl --- *}
    {if !empty($dialog['medium'])}<tr><td colspan='2'>
        <fieldset><legend>{$dialog['medium'][2]}</legend>
            {html_radios name=$dialog['medium'][0] options=$dialog['mediumLi'][1]    selected=$dialog['medium'][1] separator='<br />'}
        </fieldset>
    </td></tr>{/if}

{* --- Restaurierungsbericht --- *}
    {if isset($dialog['rest_report'])}<tr><td colspan='2'><label for='rreport'>
            {$dialog['rest_report'][2]}:</label><br />
            <textarea class='small' id='rreport' name='rest_report'>{$dialog['rest_report'][1]}</textarea>
    </td></tr>{/if}

{* --- Alte Signatur --- *}
    {if isset($dialog['oldsig'])}<tr>
        <td class='re'><label for='osig'>{$dialog['oldsig'][2]}:&nbsp;</label></td>
        <td><input class='halb' type='text' id='osig' name='oldsig' value="{$dialog['oldsig'][1]}" /></td>
    </tr>{/if}

{* --- notiz --- *}
    {if isset($dialog['notiz'])}<tr>
        <td colspan='2'><label for='note'>
            {$dialog['notiz'][2]}:&nbsp;</label><br />
            <textarea class='small' id='note' name='notiz'>{$dialog['notiz'][1]}</textarea>
        </td>
    </tr>{/if}

{* --- isvalid --- *}
    {if isset($dialog['isvalid'])}<tr>
        <td colspan='2'>
            <input
                type="checkbox"
                id='valid'
                name="isvalid"
                {if $dialog['isvalid'][1]}
                    checked="checked"
                {/if}
                value='true' />
                <label for='valid'>&nbsp;{$dialog['isvalid'][2]}</label>
        </td>
    </tr>{/if}

    <tr><td></td>
        <td class="re">
        <input type='submit' name='submit' />
        <input type='hidden' name='sektion' value='i_fkop' />
        <input type='hidden' name='aktion' value='{$aktion}' />
    </tr>
</table>
</div>

<!-- Absendebutton und Kramzeug -->

</form>
<div style='clear:both'>&nbsp;</div>