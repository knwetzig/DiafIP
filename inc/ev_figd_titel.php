<?php
/**************************************************************
*   Eventhandler für die Titelverwaltung
*
$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

// Überschrift
$smarty->assign('dialog', getStringList(array(4010,4011)));
$smarty->display('figd_titel.tpl');

if (isset($_POST['aktion'])?$_POST['aktion']:'') {
    $smarty->assign('aktion', $_POST['aktion']);

    // switch:action => add | edit | search | del
    switch($_POST['aktion']) {
        case "add":
            $nTit = new Titel;
            if ($_POST['form']) {
                // Formular anfordern
                $nTit->addTitel(false);
                $myauth->setAuthData('obj', serialize($nTit));
            } else {
                $nTit = unserialize($myauth->getAuthData('obj'));
                $nTit->addTitel(true);  // Auswertezweig
            }
        break; // Ende add

        case "edit" :
            if ($_POST['form']) {
                $eTit = new Titel;
                $eTit->getTitel($_POST['tid']);
                $myauth->setAuthData('obj', serialize($eTit));
                // Formular anfordern
                $eTit->editTitel(false);
            } else {
                // Auswertezweig
                $eTit = unserialize($myauth->getAuthData('obj'));
                $eTit->editTitel(true);
                $eTit->setTitel();
            }
        break; // ende edit

        case 'search' :
            if (isset($_POST['sstring'])) {
                $str = normtext($_POST['sstring']);
                $myauth->setAuthData('search', $str);
                $tlist = Titel::searchTitel($str);
                if ($tlist AND is_array($tlist)) {
                    // Ausgabe
                    foreach(($tlist) as $nr) {
                        $titel = new Titel($nr);
                        $titel->view();
                    }
                } else {
                    // no result
                }
            }
        break;

        case 'del' :
            // Löschen ohne Formular via _GET
            // macht im Moment nichts
        break;

    } // ende SWITCH
} // end action

/************************************************************
* Ab hier beginnt der optionale Teil der Sektion
*
************************************************************/

?>