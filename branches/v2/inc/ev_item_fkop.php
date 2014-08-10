<?php
/*****************************************************************************
    Eventhandler für Aktionen der Filmkopien (Film + Medien)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *********************************************************/

if (!$myauth->checkAuth()) feedback(108, 'error');

// Überschrift
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich', d_feld::getString(4038)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'i_fkop'),
        new d_feld('add', true, EDIT, null)
    ));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

// --- Verteiler ---
if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') :
    $smarty->assign('aktion', $_REQUEST['aktion']);

    switch(isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') :
        case "add":
            if (isset($_POST['form'])) :
                $ifk = new FilmKopie;        // Formular anfordern
                $ifk->add(false);
            else :
                $ifk = unserialize($myauth->getAuthData('obj'));
                $ifk->add(true);       // Auswertezweig
                $ifk->view();
            endif;
        break;

        case "edit" :
            if (isset($_POST['form'])) :
                $ifk = new FilmKopie($_POST['id']);
                $ifk->edit(false); // Formular anfordern
            else :                 // Auswertezweig
                $ifk = unserialize($myauth->getAuthData('obj'));
                $ifk->edit(true);
                $erg = $ifk->set();
                if ($erg) feedback($erg,'error');
                $ifk->view();
            endif;
        break;	// Ende edit

        case "search" :
            if (isset($_POST['sstring'])) :
                $str = $_POST['sstring'];
                $myauth->setAuthData('search', $str);
                $list = FilmKopie::search($str);
                if (!empty($list) AND is_array($list)) :
                    // Ausgabe
                    $bg = 1;
                    foreach (($list) as $nr) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $item = new FilmKopie($nr);
                        $item->sview();
                    endforeach;
                else :
                    feedback(102, 'hinw'); // kein Erg.
                endif;
            endif;
        break;

        case "del" :
            $ifk = new FilmKopie($_POST['id']);
            $erg = $ifk->del();
            if (empty($erg)) feedback(3, 'erfolg'); else feedback($erg, 'error');
        break;

        case 'view' :
            $ifk = new FilmKopie((int)$_REQUEST['id']);
            $ifk->view();
        break;

        case 'addImage' :
            /** --- BAUSTELLE --- **/
            $img = new Bild();
            $img->add();
        break;

    endswitch;
endif;
?>