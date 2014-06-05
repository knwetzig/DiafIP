{**************************************************************

    Template für den Eingabedialog-Namen
    
call:   person_class.php
class:  Namen
proc:   edit
param:  array([0] = Datenfeldname, [1] = inhalt, [2] = label, [3] = tooltip)

$Rev:  $
$Author:  $
$Date:  $
$URL:  $

**************************************************************}

<form action='{$dlg['phpself']}' method='post'>
  <fieldset>
    <legend>{$dialog['kopf'][2]}</legend>
      <table><colgroup><col><col><col><col></colgroup>
<!-- Vorname/Name -->
        <tr>
{if isset($dialog['vname'])}
          <td>{$dialog['vname'][2]}</td>
          <td><input type='text' name="{$dialog['vname'][0]}" value="{$dialog['vname'][1]}" /></td>
{/if}
{if isset($dialog['name'])}
          <td>{$dialog['name'][2]}</td>
          <td><input type='text' name="{$dialog['name'][0]}" value="{$dialog['name'][1]}" /></td>
{/if}
        </tr>

<!-- Notiz -->
{if isset($dialog['notiz'])}
        <tr class="darkBG">
          <td class="top">{$dialog['notiz'][2]}</td>
          <td colspan=3>
            <textarea
              name="{$dialog['notiz'][0]}"
              cols='60'
              rows='10'
              wrap='soft'
            >{$dialog['notiz'][1]}</textarea>
          </td>
        </tr>
{/if}

        <tr>
          <td></td>
          <td><input type='submit' name='submit' /></td>
        </tr>
      </table>
    </fieldset>
    <input
      type='hidden'
      name='sektion'
      value='person'
    />
    <input
      type='hidden'
      name='aktion'
      value='{$aktion}'
    />
</form>