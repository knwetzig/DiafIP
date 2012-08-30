{**************************************************************

Smarty-Template für die Bearbeitung/Neuanlage von Filmtiteln

$Rev: 5$
$Author: mortagir@gmail.com   		$
$Date: 2012-07-31 22:11:39 +0#		$
$URL$

***** (c) DIAF e.V. *******************************************}

<form method='post'><fieldset><legend>{$dialog['bereich'][2]}</legend>
    <table><colgroup><col><col></colgroup>
        {if isset($dialog['titel'])}<tr>
            <td>{$dialog['titel'][2]}</td>
            <td><input type='text' name='titel' value="{$dialog['titel'][1]}" /></td>
        {/if}</tr>

        {if isset($dialog['utitel'])}<tr>
            <td>{$dialog['utitel'][2]}</td>
            <td><input type='text' name='utitel' value="{$dialog['utitel'][1]}" /></td>
        {/if}</tr>

        {if isset($dialog['atitel'])}<tr>
            <td>{$dialog['atitel'][2]}</td>
            <td><input type='text' name='atitel' value="{$dialog['atitel'][1]}" /></td>
        {/if}</tr>

        {if isset($dialog['stitel'])}<tr>
            <td>{$dialog['stitel'][2]}</td>
            <td><select size='1' name='sid'>
                <option>-- Keine Serie --</option>
                {foreach from=$serTitel item=titel key=id}
                    <option {if $id == $dialog['sid'][1]} selected=selected {/if} value='{$id}'>{$titel}</option>
                {/foreach}
            </select></td>
        {/if}</tr>

        {if isset($dialog['sfolge'])}<tr>
            <td>{$dialog['sfolge'][2]}</td>
            <td><input type='text' name='sfolge' value="{$dialog['sfolge'][1]}" /></td>
        {/if}</tr>

        <tr>
            <td colspan="2" class="re"><input type='submit' name='submit' /></td>
        </tr>
    </table></fieldset>
    <input type='hidden' name='sektion' value='titel' />
    <input type='hidden' name='aktion' value='{$aktion}' />
</form>