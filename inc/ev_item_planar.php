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
        new d_feld('bereich', d_feld::getString(4028)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'i_planar'),
        new d_feld('add', true, EDIT, null)
    ));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_POST['aktion'])?$_POST['aktion']:'') :
    $smarty->assign('aktion', $_POST['aktion']);

    switch(isset($_POST['aktion'])?$_POST['aktion']:'') :
        case "add":
            if (isset($_POST['form'])) :
                $i2d = new Planar;        // Formular anfordern
                $i2d->add(false);
            else :
                $i2d = unserialize($myauth->getAuthData('obj'));
                $i2d->add(true);       // Auswertezweig
                $i2d->view();
            endif;
            break;

        case "edit" :
            if (isset($_POST['form'])) :
                $i2d = new Planar($_POST['id']);
                $i2d->edit(false); // Formular anfordern
            else :                 // Auswertezweig
                $i2d = unserialize($myauth->getAuthData('obj'));
                $i2d->edit(true);
                $erg = $i2d->set();
                if ($erg) :
                    fehler($erg);
                    exit;
                endif;
                $i2d->view();
            endif;
        break;	// Ende edit

        case "search" :
            if (isset($_POST['sstring'])) :
                $str = $_POST['sstring'];
                $myauth->setAuthData('search', $str);
                $list = Planar::search($str);
                if (!empty($list) AND is_array($list)) :
                    // Ausgabe
                    $bg = 1;
                    foreach(($list) as $nr) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $item = new Planar($nr);
                        $item->view();
                    endforeach;
                else :
                    fehler(102); // kein Erg.
                endif;
            endif;
        break;

        case "del" :
            $i2d = new Planar($_POST['id']);
            $erg = $i2d->del();
            if(empty($erg)) erfolg(); else fehler($erg);
        break;

        case 'view' :
            $i2d = new PLanar((int)$_REQUEST['id']);
            $i2d->view();
        break;

        case 'addImage' :
            /** --- BAUSTELLE --- **/
            $img = new Bild();
            $img->add();
        break;

    endswitch;
endif;
?>