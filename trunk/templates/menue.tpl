{***********************************+++************************
    Enthält die statische Menüstruktur im linken Bereich
    der Seite einschließlich der DB-Statistik
    Daten werden an main.php und anschließend an index.php
    via POST weitergegeben.

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div id='menue'>
    <form method='post'>
        <button class='noBG'><img src='images/diaf.png' alt='DIAF' /></button>
        <br /><br />
        <button name='sektion' value='person'>{$dlg[3]}</button>
        <button name='sektion' value='film'>{$dlg[1]}</button>
        <button name='sektion' value='i_planar'>{$dlg[2]}</button>
        <button name='sektion'  value='admin'>{$dlg[6]}</button>
        <span class='note' style='padding-top:15px;'><br /><br />{$dlg[8]}<br /></span>
        <button name='aktion' value='logout'>{$dlg[5]}</button>
    </form>
</div>

<div id='status' class='note'>
    <b>{$dlg[7]}</b>
    <table>
{foreach from=$stat item=wert key=schluessel}
    <tr><td>{$schluessel}:</td><td class='re'>{$wert}</td></tr>
{/foreach}  </table><br />
    <img src='../../openbsdpower.gif' alt='powered by OpenBSD' width='100px' />
</div>