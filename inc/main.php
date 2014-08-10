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

if (!empty($_REQUEST)) :
    /* Da hier offensichtlich was steht wird versucht die 'sektion'
        zuzuweisen und evt. eine id zu ermitteln
        zulässige Parameter:  $_REQUEST['sektion'] = 'F' | $_REQUEST['F'] = 123
        Beides würde and den Eventhandler filmogr. Daten übergeben werden */

// SONDERFALL: sektion='P' & aktion='extra' -> PName->add()
    if (!empty($_POST['sektion']) and !empty($_POST['aktion']) and $_POST['sektion'] == 'P'
        and $_POST['aktion'] == 'extra') $_REQUEST['sektion'] = 'N';

// Variante: $_REQUEST['F'] = 123 ohne weitere Parameter
    $nr = intval(current($_GET));
    if (Entity::IsInDB($nr,key($_GET))) :
        $_REQUEST['id'] = $nr;
        $_REQUEST['aktion'] = 'view';
        $_REQUEST['sektion'] = key($_REQUEST);
    endif;

// Variante: Es wurde im Suchfeld eine Nr. eingegeben
    if (!empty($_POST['sstring']) AND is_numeric($_POST['sstring'])) :
        $nr = intval($_POST['sstring']);
        $bereich = Entity::getBereich($nr);
        if ($bereich) :
            unset ($_POST, $_REQUEST['sstring']);
            $_REQUEST['sektion'] = $bereich;
            $_REQUEST['aktion'] = 'view';
            $_REQUEST['id'] = $nr;
        endif;
    endif;
endif;

// Variante: $_REQUEST['sektion'] = 'F' und Auswertung vorige
if (isset($_REQUEST['sektion']) AND isset($datei[$_REQUEST['sektion']])) :
    if (!empty($_REQUEST['aktion'])) $smarty->assign('aktion', $_REQUEST['aktion']);
    $smarty->assign('sektion', $_REQUEST['sektion']);
    include $datei[$_REQUEST['sektion']];
else :
    // mehrsprachige Vorgabeseite
    $data = $db->extended->getOne(
        'SELECT '.$myauth->getAuthData('lang').' FROM s_strings WHERE id = 13;');
    IsDbError($data);
    echo $data;
endif;
echo "</div>";
?>
