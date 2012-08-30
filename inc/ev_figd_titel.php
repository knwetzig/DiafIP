<?php
/**************************************************************
Eventhandler für die Titelverwaltung

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if(!$myauth->checkAuth()) :
    fehler(108);
    die();
endif;

// Überschrift
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich', d_feld::getString(4010)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'titel'),
        new d_feld('add', true, EDIT, null, 4023)));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');


if (isset($_POST['aktion'])?$_POST['aktion']:'') :
    $smarty->assign('aktion', $_POST['aktion']);

    // switch:action => add | edit | search | del | view
    switch($_POST['aktion']) :
        case "add":
            if(!isBit($myauth->getAuthData('rechte'), EDIT)) break;
            if (isset($_POST['form'])) {
                // Formular anfordern
                $nt = new Titel;
                $nt->addTitel(false);
            } else {
                $nt = new Titel;
                $nt->addTitel(true);  // Auswertezweig
                $nt->view();
            }
        break; // Ende add

        case "edit" :
            if(!isBit($myauth->getAuthData('rechte'), EDIT)) break;
            if (isset($_POST['form'])) {
                $eTit = new Titel;
                $eTit->getTitel($_POST['tid']);
                // Formular anfordern
                $eTit->editTitel(false);
            } else {                // Auswertezweig
                $eTit = unserialize($myauth->getAuthData('obj'));
                $eTit->editTitel(true);
                if($eTit->setTitel()) {
                    fehler(1);
                    die();
                }
                $eTit->getTitel($eTit->id);
                $eTit->view();
            }
        break; // --ende edit--

        case 'search' :
            if (isset($_POST['sstring'])) {
                $str = normtext($_POST['sstring']);
                $myauth->setAuthData('search', $str);
                $tlist = Titel::searchTitel($str);
                if (!empty($tlist) AND is_array($tlist)) {
                    // Ausgabe
                    $bg = 1;
                    foreach(($tlist) as $nr) {
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $titel = new Titel($nr);
                        $titel->view();
                    }
                } else {
                    fehler(102); // kein Erg.
                }
            }
        break;

        case 'del' :
            // Löscht den Titel unmittelbar aus der DB!
            if(!isBit($myauth->getAuthData('rechte'), DELE)) break;
            $ti = new Titel($_POST['tid']);
            $ti->delTitel();
        break;

        case 'view' :
            $ti = new Titel((int)$_POST['tid']);
            $ti->view();
        break;

    endswitch;
endif; // action

/************************************************************
* Ab hier beginnt der optionale Teil der Sektion
************************************************************/

?>