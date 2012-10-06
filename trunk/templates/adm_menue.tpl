<div class='bereich'>{$dlg[6]}</div>
{**********************************************************
   Menüseite für die Administration

$Rev$
$Author$
$Date: 2012-08-01 18:03:19 +0200 (#$
$URL$

***** (c) DIAF e.V. *******************************************}
<table style='margin-top:50px; margin-left:50px'><tr>

{*links*}
    <td>
        <form method="post">
            <fieldset><legend>&nbsp;Administration&nbsp;</legend>
                <button name='site' value='self'>Password&nbsp;&auml;ndern</button>
                <br />

                <button name='site' value='user'>Nutzerverwaltung</button>
            </fieldset>
            <input type='hidden' name='sektion' value='admin' />
        </form>
    </td>

{*rechts*}
    <td>
        <form method="post">
            <fieldset><legend>&nbsp;Voreinstellungen/Listen&nbsp;</legend>
                <button name="site" value="alias">Alias verwalten</button><br />
                <button name="site" value="orte">Orte verwalten</button>
                <input type='hidden' name='sektion' value='admin' />
        </form>

    </td>
</tr></table>