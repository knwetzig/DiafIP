<?php
/*****************************************************************************
    Eventhandler für Aktionen der Planen-Objekte (Plakate/ Dok usw.))

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *********************************************************/

if(!$myauth->checkAuth()) :
    fehler(108);
    exit;
endif;

// Überschrift
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich', d_feld::getString(4000)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'i_planar'),
        new d_feld('add', true, EDIT, null)
    ));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_POST['aktion'])?$_POST['aktion']:'') :
    $smarty->assign('aktion', $_POST['aktion']);
    // switch:action => add | edit | search | del | view
    switch(isset($_POST['aktion'])?$_POST['aktion']:'') :
        case "add":
/**
       if (isset($_POST['form'])) {
                $film = new Film;        // Formular anfordern
                $film->add(false);
            } else {
                $film = unserialize($myauth->getAuthData('obj'));
                $film->add(true);       // Auswertezweig
                $film->view();
            }
**/
        break; // Ende add

        case "edit" :
/**
            if (isset($_POST['form'])) {
                $film = new Film($_POST['fid']);
                $film->edit(false); // Formular anfordern
            } else {                // Auswertezweig
                $film = unserialize($myauth->getAuthData('obj'));
                $film->edit(true);
                $erg = $film->set();
                if ($erg) :
                    fehler($erg);
                    exit;
                endif;
                $film->view();
            }
**/
        break;	// Ende edit

        case "search" :
/**
            if (isset($_POST['sstring'])) :
                $str = $_POST['sstring'];
                $myauth->setAuthData('search', $str);
                $tlist = Film::search($str);
                if (!empty($tlist) AND is_array($tlist)) :
                    // Ausgabe
                    $bg = 1;
                    foreach(($tlist) as $nr) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $film = new Film($nr);
                        $film->view();
                    endforeach;
                else :
                    fehler(102); // kein Erg.
                endif;
            endif;
**/
        break;

        case "del" :
            //$film = new Film($_POST['fid']);
            //$erg = $film->del();
            //if(empty($erg)) erfolg(); else fehler($erg);
        break;

        case 'view' :
            //$item = new xxx((int)$_POST['fid']);
            //$item->view();
        break;

        case 'addFid' :
            $smarty->assign('aktion','edit');
            $film->edit(false);
        break;

        case 'delFid' :
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