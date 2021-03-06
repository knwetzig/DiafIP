{******************************************************************************
Aufruf: adm_self.php
class:
proc:
param:  array([0] => Datenfeldname, [1] => inhalt, [2] => label, [3] = tooltip)

$Rev:: 98                      $:  Revision der letzten Übertragung
$Author:: knwetzig             $:  Autor der letzten Übertragung
$Date:: 2014-08-27 00:55:16 +0#$:  Datum der letzten Übertragung
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_setpass.tpl $

***** (c) DIAF e.V. *******************************************}

<div id='bereich'>{$dialog['bereich'][2]}</div>

<form action='{$dlg['phpself']}' method='post'>
    <fieldset><legend>&nbsp;{$dialog['bereich'][2]}&nbsp;</legend>
        <table style='padding:5px'>
            <tr>
                <td class='re'><label for='name'>{$dialog['name'][2]}</label></td>
                <td id='name' class='fett'>{$dialog['name'][1]}</td>
            </tr>
            <tr>
                <td class='re'>{$dialog['rname'][2]}</td>
                <td>{$dialog['rname'][1]}</td>
            </tr>
            <tr><td colspan='2'>&nbsp;</td></tr>
            <tr>
                <td colspan='2' class='re'>
            </tr>
            <tr>
                <td class='re'>
                    <label for='password'>{$dialog['pwd'][2]}</label>
                </td>
                <td>
                    <input
                        type='password'
                        name='password'
                        id='password' />
                </td>
            </tr>
            <tr>
                <td class='re'>
                    <label for='password2'>{$dialog['pwd2'][2]}</label>
                </td>
                <td>
                    <input
                        type='password'
                        name='password2'
                        id='password2' />
                </td>
            </tr>
            <tr>
                <td colspan='2'>&nbsp;</td>
            </tr>
            <tr>
                <td colspan='2' class='re'>
                    <button
                        type='submit'
                        name='{$dialog['submit'][0]}'
                        value='{$dialog['submit'][1]}'
                    >{$dialog['submit'][2]}</button>
                </td>
            </tr>
        </table>
    </fieldset>
    <input type='hidden' name='sektion' value='admin' />
    <input type='hidden' name='site' value='self' />
</form>
