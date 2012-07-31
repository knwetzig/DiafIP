<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev::                         $:  Revision der letzten Übertragung
$Author:: Knut Wetzig          $:  Autor der letzten Übertragung
$Date:: 2012-07-31             $:  Datum der letzten Übertragung
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
