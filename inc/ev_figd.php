<?php
/**************************************************************
Eventhandler für die Gegenstände im Archiv/Depotbereich

$Rev::                         $:  Revision der letzten Übertragung
$Author:: Knut Wetzig          $:  Autor der letzten Übertragung
$Date:: 2012-07-31             $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

$smarty->display('figd.tpl');

switch(isset($_POST['aktion'])?$_POST['aktion']:'') {
    case "add":
        if(!isset($_POST['submit'])) { // Formular anzeigen
        /* Auswahl eines Titels aus der Titeltabelle und ->
        $smarty->assign(___Titelnummer___); */
        $smarty->display('figd_film_add.tpl');
        } else {
            // Auswertung evt. Eingaben
            unset($_POST['aktion']);
        }
    break; // Ende add

    case "edit" :
        if (!isset($_POST['submit'])) { // Formular anzeigen
//$smarty->assign('titelnr', ???);
            $smarty->display('figd_film_edit.tpl');
        } else {
            // Auswertung evt. Eingaben
            unset($_POST['aktion']);
        }
    break;	// Ende edit

    case "del" :   // Löschen ohne Formular via _GET
        // derzeit nicht implementiert
        unset($_POST['aktion']);
    break;
} // ende SWITCH

/***********************************************************
* AB HIER BEGINNT DIE ANZEIGE DER SEKTION                  *
************************************************************/
if (!isset($_POST['aktion'])) {


} // Ende Anzeige
?>