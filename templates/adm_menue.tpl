<div class='bereich'>{$dlg[6]}</div>
{**********************************************************
*   Menüseite für die Administration
*
*   Autor:      Knut Wetzig
*   Copyright:  DIAF e.V.
*   Date:       20120717
*
*   ToDo:
**********************************************************}

<form method='post' style='margin-top:50px; margin-left:50px'>
    <p>
        <button name='site' value='self'>Password&nbsp;&auml;ndern</button>
    </p>

    <fieldset><legend>&nbsp;Voreinstellungen/Listen&nbsp;</legend>
        <button name="site" value="alias">Alias verwalten</button><br />
        <button name="site" value="orte">Orte verwalten</button>
    </fieldset>
    <br />

    <fieldset><legend>&nbsp;Administration&nbsp;</legend>
        <button name='site' value='user'>Nutzerverwaltung</button>
    </fieldset>
    <input type='hidden' name='sektion' value='admin' />
</form>
