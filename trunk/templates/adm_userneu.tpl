{******************************************************************************
Aufruf: adm_user.php

section:    admin
site:       user

    Für diesen Bereich ist keine Internationalisierung vorgesehen
******************************************************************************}
<div class='bereich'>Benutzerverwaltung</div>
<form method='post'>
    <fieldset>
        <legend>&nbsp;Neuen User anlegen&nbsp;</legend>
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
        <input type='hidden' name='section' value='admin' />
        <input type='hidden' name='site' value='user' />
    </fieldset>
</form>
