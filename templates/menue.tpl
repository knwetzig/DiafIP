{**************************************************************
    Enthält die statische Menüstruktur im linken Bereich
    der Seite

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div id='menue'>
    <form action='index.php' method='get'>
        <button class='noBG'><img src='images/diaf.png' alt='DIAF' /></button>
        <br /><br />
        <button name='sektion' value='person'>{$dlg[3]}</button>
        <button name='sektion' value='film'>{$dlg[1]}</button>
        <button name='sektion' value='i_planar'>{$dlg[2]}</button>
        <button name='sektion' value='i_3dobj'>{$dlg[8]}</button>
        <button name='sektion' value='admin'>{$dlg[6]}</button>
        <span class='note' style='padding-top:15px;'><br /><br />{$dlg[9]}<br /></span>
        <button name='aktion' value='logout'>{$dlg[5]}</button>
    </form>
</div>