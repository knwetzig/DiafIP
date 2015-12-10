{**********************************************************

   Menüseite für die Administration
   (Auswahl der Elemente/Presetlisten)

$Rev: 77 $
$Author: knwetzig $
$Date: 2014-08-12 18:32:46 +0200 (Tue, 12. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_menue.tpl $

***** (c) DIAF e.V. *******************************************}

<div id='bereich'>{$dlg['pref']}</div>

<form method="post">
    <fieldset><legend>Verwaltung</legend>
        <button name='site' value='self'>Password</button>
        <button name='site' value='user'>Nutzer</button>
        <button name='site' value='string'>&Uuml;bersetzung</button>
    </fieldset>
     <fieldset><legend>Listen</legend>
        <button name="site" value="orte">Orte verwalten</button>
        <button name="site" value="lort">Lagerorte verwalten</button>
    </fieldset>
    <fieldset><legend>Import</legend>
        <button name='site' value="fgd_imp">Film importieren</button>
    </fieldset>
    <input type="hidden" name="sektion" value="admin" />
    <input type="hidden" name="aktion" value="" />
</form>

<p>
    <a href='https://github.com/knwetzig/diafip' target='_new'>Projektseite</a><br />
    <a href='https://github.com/knwetzig/diafip/blob/wiki/Leitfaden.md' target='_new'>Leitfaden zur Handhabung</a><br>
    <a href='data/DIFA DB Filmdatenexport.xml' target="_new">Originaldaten DEFA-Stiftung</a>
</p>