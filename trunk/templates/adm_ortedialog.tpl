{**************************************************************
Smarty-Template für Erstellung/Bearbeitung von Ortsnamen

class: s_location_class.php
proc:  editOrt
param:  array([0] => Datenfeldname, [1] => inhalt, [2] => label, [3] => tooltip)

$Rev::                         $:  Revision der letzten Übertragung
$Author:: Knut Wetzig          $:  Autor der letzten Übertragung
$Date:: 2012-07-31             $:  Datum der letzten Übertragung
$URL$

ToDo: Überarbeitung und statt diesem menü das adm_dialog.tpl verwenden

***** (c) DIAF e.V. *******************************************}

<br />
<form method='post'>
    <fieldset>
        <legend>Verwaltung der Ortsnamen</legend>
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