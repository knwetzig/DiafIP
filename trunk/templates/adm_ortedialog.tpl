{**************************************************************

class: s_location_class.php
proc:  editOrt
param:
    array dialog[0] = Datenfeldname
                [1] = inhalt
                [2] = label
                [3] = tooltip
**************************************************************}
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