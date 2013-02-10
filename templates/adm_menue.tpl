{**********************************************************

   Menüseite für die Administration
   (Auswahl der Elemente/Presetlisten)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div class='bereich'>{$dlg[6]}</div>
<div class='bereich_fuss'></div>

<table><tr>
    <td class="top">    {*links*}
        <form method="post">
            <fieldset><legend>&nbsp;Verwaltung&nbsp;</legend>
                <button
                    name='site'
                    value='self'>
                    Password&nbsp;&auml;ndern
                </button>
                <br />
                <button
                    name='site'
                    value='user'>
                    Nutzerverwaltung
                </button>
            </fieldset>
            <input type='hidden' name='sektion' value='admin' />
        </form>
    </td>

    <td class="top">     {*rechts*}
        <form method="post">
            <fieldset><legend>&nbsp;Listen&nbsp;</legend>
                <button name="site" value="alias">Alias verwalten</button><br />
                <button name="site" value="orte">Orte verwalten</button><br />
                <button name="site" value="lort">Lagerorte verwalten</button>
                <input type='hidden' name='sektion' value='admin' />
        </form>
    </td>
</tr></table>
