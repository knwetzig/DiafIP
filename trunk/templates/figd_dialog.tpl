{**************************************************************

Smarty-Template für die Bearbeitung/Neuanlage von filmografischen Datensätzen

$Rev: 5$
$Author: mortagir@gmail.com   		$
$Date: 2012-07-31 22:11:39 +0#		$
$URL$

***** (c) DIAF e.V. *******************************************}

<form method='post'>
    <table><colgroup><col><col></colgroup><tr>
<td><!-- linke Seite -->
    <table>
{* --- titel --- *}
        {if isset($dialog['titel'])}<tr>
            <td class="re">{$dialog['titel'][2]}:</td>
            <td><input type='text' name='titel' value="{$dialog['titel'][1]}" /></td>
        </tr>{/if}

{* --- utitel --- *}

        {if isset($dialog['utitel'])}<tr>
            <td class="re">{$dialog['utitel'][2]}:</td>
            <td><input type='text' name='utitel' value="{$dialog['utitel'][1]}" /></td>
        </tr>{/if}

{* --- atitel --- *}
        {if isset($dialog['atitel'])}<tr>
            <td class="re">{$dialog['atitel'][2]}:</td>
            <td><input type='text' name='atitel' value="{$dialog['atitel'][1]}" /></td>
        </tr>{/if}

{* --- stitel --- *}
        {if isset($dialog['stitel'])}<tr>
            <td class="re">{$dialog['stitel'][2]}:</td>
            <td><select size='1' name='sid'>
                <option>-- Keine Serie --</option>
                {foreach
                    from=$dialog['serTitel'][1]
                    item=titel key=id}
                    <option {if $id == $dialog['sid'][1]} selected=selected {/if} value='{$id}'>{$titel}</option>
                {/foreach}
            </select></td>
        </tr>{/if}

{* --- sfolge --- *}
        {if isset($dialog['sfolge'])}<tr>
            <td class="re">{$dialog['sfolge'][2]}:</td>
            <td><input type='text' name='sfolge' value="{$dialog['sfolge'][1]}" /></td>
        </tr>{/if}


{* --- Beschreibung --- *}
        {if isset($dialog['inhalt'])}<tr>
            <td class="re">{$dialog['inhalt'][2]}:</td>
            <td><textarea
                cols="40"
                rows="10"
                wrap="soft"
                name={$dialog['inhalt'][0]}
                >{$dialog['inhalt'][1]}</textarea></td>
        </tr>{/if}

{* --- Quellen --- *}
        {if isset($dialog['quellen'])}<tr>
            <td class="re">{$dialog['quellen'][2]}:</td>
            <td><textarea
                cols="40"
                rows="1"
                name={$dialog['quellen'][0]}
                >{$dialog['quellen'][1]}</textarea>
            </td>
        </tr>{/if}

{* --- Anmerkungen --- *}
        {if isset($dialog['anmerk'])}<tr>
            <td class="re">
                {$dialog['anmerk'][2]}:<br />
                <span class="note">(public)</span>
            </td>
            <td><textarea
                cols="40"
                rows="8"
                wrap="soft"
                name={$dialog['anmerk'][0]}
                >{$dialog['anmerk'][1]}</textarea></td>
        </tr>{/if}
    </table>
</td><!-- ende links -->

<td style="padding-left:20px"><!-- rechte Seite -->
    <table>
{* --- Produktionjahr --- *}
        {if isset($dialog['prod_jahr'])}<tr>
            <td class="re">{$dialog['prod_jahr'][2]}:</td>
            <td><input type="text" name={$dialog['prod_jahr'][0]} value="{$dialog['prod_jahr'][1]}" /></td>
        </tr>{/if}

{* --- Thema --- *}
        {if isset($dialog['thema'])}<tr>
            <td class="re">{$dialog['thema'][2]}:</td>
            <td><input type="text" name={$dialog['thema'][0]} value="{$dialog['thema'][1]}" /></td>
        </tr>{/if}

{* --- Gattung --- *}
        {if isset($dialog['gattung'])}<tr>
            <td class="re">{$dialog['gattung'][2]}:</td>
            <td>
                {html_options
                    name=$dialog['gattung'][0]
                    options=$dialog['gattLi'][1]
                    selected=$dialog['gattung'][1]}
            </td>
        </tr>{/if}


{* --- Prod_technik --- *}
        {if isset($dialog['prodtech'])}<tr>
            <td class="re">{$dialog['prodtech'][2]}:</td>
            <td>{html_checkboxes
                    name=$dialog['prodtech'][0]
                    options=$dialog['prodTecLi'][1]
                    selected=$dialog['prodtech'][1]
                    separator= "<br />"}
            </td>
        </tr>{/if}

{* --- Laufzeit --- *}
        {if isset($dialog['laenge'])}<tr>
            <td class="re">{$dialog['laenge'][2]}:</td>
            <td><input
                type="text"
                name="{$dialog['laenge'][0]}"
                value="{$dialog['laenge'][1]}"
                onmouseover="return overlib('{$dialog['laenge'][3]}',DELAY,1000,WIDTH,600);"
                onmouseout="return nd();"
            /></td>
        </tr>{/if}

{* --- Age --- *}
        {if isset($dialog['fsk'])}<tr>
            <td class="re">{$dialog['fsk'][2]}:</td>
            <td><input type="text" name={$dialog['fsk'][0]} value="{$dialog['fsk'][1]}" /></td>
        </tr>{/if}

{* --- Praedikat --- *}
        {if isset($dialog['praedikat'])}<tr>
            <td class="re">{$dialog['praedikat'][2]}:</td>
            <td>
                {html_options
                    name=$dialog['praedikat'][0]
                    options=$dialog['praedLi'][1]
                    selected=$dialog['praedikat'][1]}
            </td>
        </tr>{/if}

{* --- Uraufführung --- *}
        {if isset($dialog['urauff'])}<tr>
            <td class="re">{$dialog['urauff'][2]}:</td>
            <td><input
                type="text"
                name={$dialog['urauff'][0]}
                value="{$dialog['urauff'][1]}" /></td>
        </tr>{/if}

{* --- Mediaspezifikation --- *}
        {if isset($dialog['mediaspezi'])}<tr>
            <td class="re">{$dialog['mediaspezi'][2]}:</td>
            <td>{html_checkboxes
                    name=$dialog['mediaspezi'][0]
                    options=$dialog['mediaSpezLi'][1]
                    selected=$dialog['mediaspezi'][1]
                    separator= "&nbsp;"}</td>
        </tr>{/if}

{* --- Notiz --- *}
        {if isset($dialog['notiz'])}<tr>
            <td class="re">
                {$dialog['notiz'][2]}:<br />
                <span class="note">(intern)</span>
            </td>
            <td><textarea
                cols="29"
                rows="8"
                wrap="soft"
                name={$dialog['notiz'][0]}
                >{$dialog['notiz'][1]}</textarea></td>
        </tr>{/if}

        <tr>
            <td colspan="2" class="re"><input type='submit' name='submit' /></td>
        </tr>
    </table>
  </td><!-- rechte Seite -->
</tr></table>
    <input type='hidden' name='sektion' value='film' />
    <input type='hidden' name='aktion' value='{$aktion}' />
</form>

{* --- Besetzung --- *}
<fieldset><legend>Besetzung</legend>
  <table>
    {if isset($dialog['cast'])}{foreach from=$dialog['cast'][1] item=wert}
    <tr>
        <td class="re">{$wert['job']}:</td>
        <td>{$wert['vname']}&nbsp;{$wert['name']}</td>
        <td><form method="post"><button
                class="small"
                name="aktion"
                value="delCast" /><img src="images/del.png"
            /></button>
            <input type="hidden" name="pid" value="{$wert['pid']}" />
            <input type="hidden" name="tid" value="{$wert['tid']}" />
            <input type="hidden" name="fid" value="{$dialog['id'][1]}" />
        </form></td>
    </tr>
    {/foreach}{/if}
    <tr><form method='post'>
        <input type="hidden" name="fid" value={$dialog['id'][1]} />
        <td>{html_options
                name=tid
                options=$dialog['taetigLi'][1]}
        </td>
        <td>{html_options
                name=pid
                options=$dialog['persLi'][1]}
        </td>
        <td><button
                class='small'
                name='aktion'
                value='addCast'>
            <img src="images/add.png" alt="add" />
            </button>
            <input type="hidden" name="bereich" value="Cast" />
        </td>
    </form></tr>
  </table>
</fieldset>