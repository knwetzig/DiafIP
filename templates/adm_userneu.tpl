{******************************************************************************
Smarty-Template für die Neuanlage von Usern
Für diesen Bereich ist keine Internationalisierung vorgesehen

Aufruf: adm_user.php

section:    admin
site:       user

$Rev::                              $:  Revision der letzten Übertragung
$Author::                           $:  Autor der letzten Übertragung
$Date::                             $:  Datum der letzten Übertragung
$URL$

ToDo: Überarbeitung unter Verwendung von adm_dialog.tpl

***** (c) DIAF e.V. *******************************************}

<div class='bereich'>Benutzerverwaltung</div>
<form method='post'>
    <fieldset>
        <legend>&nbsp;Neuen Nutzer anlegen&nbsp;</legend>
        <table>
            <colgroup><col><col></colgroup>
            <tr>
                <td class='re'>Accountname:</td>
                <td><input
                    type="text"
                    name="username" />
                </td>
            </tr>
            <tr>
                <td class="re">Passwort:</td>
                <td><input
                    type="text"
                    name="pwd" />
                </td>
            </tr>
            <tr>
                <td>&nbsp;</td>
                <td class="re"><button
                    type="submit"
                    name="submit"
                    value="addUser">
                    speichern
                    </button>
            </tr>
        </table>
        <input type='hidden' name='sektion' value='admin' />
        <input type='hidden' name='site' value='user' />
    </fieldset>
</form>
