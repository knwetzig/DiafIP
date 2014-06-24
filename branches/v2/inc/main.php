<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev$
$Author$
$Date$
$URL$

Anm.: Schreibe 'sektion' und nicht 'section' und 'aktion' und nicht 'action'!!!
**************************************************************/

const ISEXIST = 'SELECT COUNT(*) FROM entity WHERE id = ?;';

echo "<div id='main'>";
if(!empty($_REQUEST)) :
    /* Da hier offensichtlich was steht wird versucht die 'sektion'
        zuzuweisen und evt. eine id zu ermitteln
        zulässige Parameter:  $_REQUEST['sektion'] = 'F' | $_REQUEST['F'] = 123
        Beides würde and den Eventhandler filmogr. Daten übergeben werden */

// Variante: $_REQUEST['F'] = 123 ohne weitere Parameter
    if(is_numeric(current($_REQUEST))) :
        // Test ob Nr. als id in der DB existiert
        $nr = (int)current($_REQUEST);
        $data = $db->extended->getRow(ISEXIST, null, $nr);
        IsDbError($data);
        if($data['count']) $_REQUEST['id'] = $nr;
    endif;

    switch(key($_REQUEST)) :
        case 'N' :                  //Namen
        case 'P' :
            $_REQUEST['sektion'] = 'P';    // Person
            break;
        case 'F' :
            $_REQUEST['sektion'] = 'F';    // filmogr.
            break;
        case 'Y' :
            $_REQUEST['sektion'] = 'Y';    // planar
            break;
        case 'Z' :
            $_REQUEST['sektion'] = 'Z';    // körper
            break;
        case 'K' :
            $_REQUEST['sektion'] = 'K';    //filmkopie
    endswitch;
endif;

// Variante: $_REQUEST['sektion'] = 'F' und A++++++++++++++-------uswertung vorige
if(isset($_REQUEST['sektion']) AND isset($datei[$_REQUEST['sektion']]))
    include $datei[$_REQUEST['sektion']];
else include 'default.php';
echo "</div>";
?>
