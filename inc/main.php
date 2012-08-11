<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev:: 5                       		$:  Revision der letzten Übertragung
$Author:: mortagir@gmail.com   		$:  Autor der letzten Übertragung
$Date:: 2012-07-31 22:11:39 +0#		$:  Datum der letzten Übertragung
$URL$

Anm.: Schreibe 'sektion' und nicht 'section'!!!
***** (c) DIAF e.V. *******************************************/

    echo "<div id='main'>";

    if(isset($_POST['sektion']) AND isset($datei[$_POST['sektion']])) {
        include $datei[$_POST['sektion']];
    } else {
        include 'default.php';
    }
    echo "</div>";
?>
