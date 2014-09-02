{**************************************************************
Smarty-Template für Erstellung/Bearbeitung von Ortsnamen

class: s_location_class.php
proc:  editOrt
param:  array([0] => Datenfeldname, [1] => inhalt, [2] => label, [3] => tooltip)

$Rev: 50 $
$Author: knwetzig $
$Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_ortedialog.tpl $

ToDo: Überarbeitung und statt diesem menü das adm_dialog.tpl verwenden

***** (c) DIAF e.V. *******************************************}

<br />
<form action='{$dlg['phpself']}' method='post'>
    <fieldset>
        <legend>Ortsnamen</legend>
        <table><colgroup><col><col></colgroup>
            <tr><!-- Land -->
                <td>Land/Bundesland</td>
                <td>
                    {html_options
                        name='land'
                        options=$llist
                        selected=$dialog['lid'][1]
                    }
                </td>
            </tr>

            <tr>
                <td>Ort/Gemeinde</td>
                <td>
                    <input
                        type='text'
                        name="{$dialog['ort'][0]}"
                        value="{$dialog['ort'][1]}"
                    />
                </td>
            </tr>
            <tr>
                <td colspan="2" class="re">
                    <button
                        type="submit"
                        name="submit"
                        value="delOrt">
                        löschen
                    </button>

                    <button
                        type="submit"
                        name="submit"
                        value="{$aktion}">
                        speichern
                    </button>
                </td>
            </tr>

        </table>
    </fieldset>
    <input type='hidden' name='sektion' value='admin' />
    <input type='hidden' name='site' value='orte' />
</form>