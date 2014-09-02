{**********************************************************

   Menüseite für die Administration
   (Auswahl der Elemente/Presetlisten)

$Rev: 77 $
$Author: knwetzig $
$Date: 2014-08-12 18:32:46 +0200 (Tue, 12. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_menue.tpl $

***** (c) DIAF e.V. *******************************************}

<div id='bereich'>{$dlg['pref']}</div>

<table><tr>
    <td class="top">    {*links*}
        <form method="post">
            <fieldset><legend>&nbsp;Verwaltung&nbsp;</legend>
                <button name='site' value='self'>
                    Password</button><br />
                <button name='site' value='user'>
                    Nutzer</button><br />
                <button name='site' value='string'>
                    &Uuml;bersetzung</button>
            </fieldset>
            <input type='hidden' name='sektion' value='admin' />
            <input type='hidden' name='aktion' value='' />
        </form>
    </td>

    <td class="top">     {*rechts*}
        <form method="post">
            <fieldset><legend>&nbsp;Listen&nbsp;</legend>
                <button name="site" value="orte">Orte verwalten</button><br />
                <button name="site" value="lort">Lagerorte verwalten</button>
                <input type="hidden" name="sektion" value="admin" />
                <input type="hidden" name="aktion" value="" />
        </form>
    </td>
</tr></table>

<div id='bereich'>&nbsp;</div>
<a href='https://code.google.com/p/diafip/' target='_new'><img src='https://ssl.gstatic.com/codesite/ph/images/phosting.ico'>&nbsp;Projektseite</a><br /><br />
<a href='https://code.google.com/p/diafip/wiji/Leitfaden' target='_new'>Leitfaden zur Handhabung</a>