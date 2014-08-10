<?php
/***************************************************************
    Eventhandler für Aktionen der Filmverwaltung

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if (!$myauth->checkAuth()) :
    feedback(108, 'error');
    exit;
endif;

// Kopfbereich
$data = a_display(array(
    // name,inhalt,rechte, optional-> $label,$tooltip,valString
    new d_feld('bereich', d_feld::getString(4008)),
    new d_feld('sstring', d_feld::getString(4011)),
    new d_feld('sektion', 'film'),
    new d_feld('add', true, EDIT, null, 4024)
));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') :
    $smarty->assign('aktion', $_REQUEST['aktion']);

// switch:action => add | edit | search | del | view
    switch(isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'view') :
        case "add":
            if (isset($_POST['form'])) {
                $film = new Film;        // Formular anfordern
                $film->add(false);
            } else {
                $film = unserialize($myauth->getAuthData('obj'));
                $film->add(true);       // Auswertezweig
                $film->view();
            }
        break; // Ende add

        case "edit" :
            if (isset($_POST['form'])) {
                $film = new Film($_POST['id']);
                $film->edit(false); // Formular anfordern
            } else {                // Auswertezweig
                $film = unserialize($myauth->getAuthData('obj'));
                $film->edit(true);
                $erg = $film->set();
                if ($erg) feedback($erg, 'error');
                $film->view();
            }
        break;	// Ende edit

        case "search" :
            if (isset($_POST['sstring'])) :
                $str = $_POST['sstring'];
                $myauth->setAuthData('search', $str);
                $tlist = new Film;
                $tlist = $tlist->search($str);
                if (!empty($tlist) AND is_array($tlist)) :
                    // Ausgabe
                    $bg = 1;
                    foreach (($tlist) as $nr) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $film = new Film($nr);
                        $film->sview();
                    endforeach;
                else :
                    feedback(102, 'hinw'); // kein Erg.
                endif;
            endif;
        break;

        case "del" :
            $film = new Film($_POST['id']);
            $erg = $film->del();
            if (empty($erg)) feedback(3, 'erfolg'); else feedback($erg, 'warng');
        break;

        case 'view' :
            $film = new Film((int)$_REQUEST['id']);
            $film->display();
        break;

        case 'addCast' :
            $film = new Film($_POST['id']);
            $film->addCast($_POST['pid'], $_POST['tid']);
            $smarty->assign('aktion','edit');
            $film->edit(false);
        break;

        case 'delCast' :
            $film = new Film($_POST['id']);
            $film->delCast($_POST['pid'], $_POST['tid']);
            $smarty->assign('aktion','edit');
            $film->edit(false);
        break;

        case 'addImage' :
            /** --- BAUSTELLE --- **/
            // aufruf von figd_dialog.tpl
            $img = new Bild();
            $img->add();
        break;

    endswitch;
endif;
?>