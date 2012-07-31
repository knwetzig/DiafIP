<?php
/**********************************************************
*
*   Das Ladeprogramm für die Hauptseite
*   Hier wird nur die "sektion" Fraktion ausgewertet
*
*   Autor:      Knut Wetzig
*   Copyright:  DIAF e.V.
*   Date:       31052012
*
**********************************************************/

    echo "<div id='main'>";

    if(isset($_POST['sektion']) AND isset($datei[$_POST['sektion']])) {
        include $datei[$_POST['sektion']];
    } else {
        include 'default.php';
    }
    echo "</div>";
?>
