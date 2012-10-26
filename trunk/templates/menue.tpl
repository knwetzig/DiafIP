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
        <p style='text-align:center; font-weight:bold'>{$dlg[0]}
        <button name='sektion' value='film'>{$dlg[1]}</button>
        <button name='sektion' value='item'>{$dlg[2]}</button>
        <button name='sektion' value='person'>{$dlg[3]}</button></p>
        <p style='text-align:center; font-weight:bold'>{$dlg[4]}
        <button name='sektion'  value='admin'>{$dlg[6]}</button>
        <button name='sektion' value='news'>Pinnwand</button>
        <button name='sektion' value='changes'>ChangeLog</button></p>
    <div class='note' style='text-align:center;'>{$dlg[8]}</div>
        <button name='aktion' value='logout'>logout</button>
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