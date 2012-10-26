<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev: 5                       		$
$Author: mortagir@gmail.com   		$
$Date: 2012-07-31 22:11:39 +0#		$
$URL$

Anm.: Schreibe 'sektion' und nicht 'section'!!!
***** (c) DIAF e.V. *******************************************/

    echo "<div id='main'>";

if(isset($_POST['aktion'])) warng('sektion:&nbsp;'.$_POST['sektion'].'&nbsp;|&nbsp;aktion:&nbsp;'.$_POST['aktion']);

    if(isset($_POST['sektion']) AND isset($datei[$_POST['sektion']]))
        include $datei[$_POST['sektion']]; else include 'default.php';
    echo "</div>";
?>
