{******************************************************************************
Smarty-Template zum bearbeiten der Accounteinstellungen

Aufruf: adm_user.php

section:    admin
site:       user

    Für diesen Bereich ist keine Internationalisierung vorgesehen
$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

***** (c) DIAF e.V. *******************************************}

<br />
<form method='post'>
    <fieldset>
        <legend>&nbsp;Accountdaten&nbsp:bearbeiten&nbsp;</legend>
        <table>
            <colgroup><col><col></colgroup>
            <tr>
                <td class='re'>Accountname:</td>
                <td><input
                    type="text"
                    name="username"
                    value="{$dialog['username']}" />
                </td>
            </tr>
            <tr>
                <td class="re">Realname:</td>
                <td><input
                    type="text"
                    name="realname"
                    value="{$dialog['realname']}" />
                </td>
            </tr>
            <tr>
                <td class='re'>Rechte:</td>
                <td>
                    {html_checkboxes
                        name= "rechte"
                        options="{$dialog['rightboxes']}"
                        selected="{$dialog['rightSel']}"
                        separator= "<br />"}
                </td>
            <tr>
                <td class="re">Notizen:</td>
                <td><textarea
                    name="notiz"
                    cols="26"
                    rows="8"
                    wrap="soft">{$dialog['notiz']}</textarea>
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="re"><button
                    type="submit"
                    name="submit"
                    value="edUser">
                    speichern
                    </button>
            </tr>
        </table>
        <input type='hidden' name='sektion' value='admin' />
        <input type='hidden' name='site' value='user' />
    </fieldset>
</form>
