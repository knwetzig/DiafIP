{**************************************************************
Smarty-Template für die Bearbeitung/Neuanlage von Filmtiteln

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:

***** (c) DIAF e.V. *******************************************}

<form method='post'>
    <fieldset>
        <legend>{$dialog[6]}</legend>
        <table><colgroup><col><col></colgroup>
            <tr>
                <td>{$dialog[0]}</td>
                <td><input type='text' name='titel' value="{$obj->titel}" /></td>
            </tr>
            <tr>
                <td>{$dialog[1]}</td>
                <td><input type='text' name='utitel' value="{$obj->utitel}" /></td>
            </tr>
            <tr>
                <td>{$dialog[2]}</td>
                <td><input type='text' name='atitel' value="{$obj->atitel}" /></td>
            </tr>
            <tr>
                <td>{$dialog[3]}</td>
                <td><select size='1' name='sid'>
                    <option value=0 >-- Keine Serie --</option>
                    {foreach from=$serTitel item=titel key=id}
                        <option {if $id == $obj->sid} selected=selected {/if} value='{$id}'>{$titel}</option>
                    {/foreach}
                </select></td>
            </tr>
            <tr>
                <td>{$dialog[4]}</td>
                <td><input type='text' name='sfolge' value="{$obj->sfolge}" /></td>
            </tr>
            <tr>
                <td>{$dialog[5]}</td>
                <td><textarea name='inhalt' cols='60' rows='15' wrap='soft'>{$obj->inhalt}</textarea></td>
            </tr>
            <tr>
                <td></td>
                <td><input type='submit' name='submit' /></td>
            </tr>
        </table>
    </fieldset>
    <input type='hidden' name='section' value='titel' />
    <input type='hidden' name='aktion' value='{$aktion}' />
</form>