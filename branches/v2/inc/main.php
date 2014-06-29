<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite

$Rev$
$Author$
$Date$
$URL$

Anm.: Schreibe 'sektion' und nicht 'section' und 'aktion' und nicht 'action'.
Der Browser wird es dir danken, indem er nicht mehr durcheinander kommt.
**************************************************************/

echo "<div id='main'>";
if(!empty($_REQUEST)) :
    /* Da hier offensichtlich was steht wird versucht die 'sektion'
        zuzuweisen und evt. eine id zu ermitteln
        zulässige Parameter:  $_REQUEST['sektion'] = 'F' | $_REQUEST['F'] = 123
        Beides würde and den Eventhandler filmogr. Daten übergeben werden */

// Variante: $_REQUEST['F'] = 123 ohne weitere Parameter
    $nr = current($_REQUEST);
    if(Entity::IsInDB($nr,key($_REQUEST))) :
        $_REQUEST['id'] = $nr;
        $_REQUEST['aktion'] = 'view';
    endif;

    switch(key($_REQUEST)) :
        case 'N' :                         //Namen
            $_REQUEST['sektion'] = 'N';
            break;
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

// Variante: Es wurde im Suchfeld eine Nr. eingegeben
    if(!empty($_POST['sstring']) AND is_numeric($_POST['sstring'])) :
        $nr = intval($_POST['sstring']);
        $bereich = Entity::getBereich($nr);
        if($bereich) :
            unset ($_POST, $_REQUEST['sstring']);
            $_REQUEST['sektion'] = $bereich;
            $_REQUEST['aktion'] = 'view';
            $_REQUEST['id'] = $nr;
        endif;
    endif;
endif;

// Variante: $_REQUEST['sektion'] = 'F' und Auswertung vorige
if(isset($_REQUEST['sektion']) AND isset($datei[$_REQUEST['sektion']]))
    include $datei[$_REQUEST['sektion']];
else include 'default.php';
echo "</div>";
?>
