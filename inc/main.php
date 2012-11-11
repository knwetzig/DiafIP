<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev$
$Author$
$Date$
$URL$

Anm.: Schreibe 'sektion' und nicht 'section' und 'aktion' AND NOT 'action'!!!
***** (c) DIAF e.V. *******************************************/

    echo "<div id='main'>";

if(isset($_POST['aktion'])) warng('sektion:&nbsp;<b>'.$_POST['sektion'].'</b>&nbsp;&nbsp;aktion:&nbsp;<b>'.$_POST['aktion'].'</b>&nbsp;');

    if(isset($_POST['sektion']) AND isset($datei[$_POST['sektion']]))
        include $datei[$_POST['sektion']]; else include 'default.php';
    echo "</div>";
?>
