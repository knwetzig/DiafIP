<?php
/*****************************************************************************
    Eventhandler für Aktionen der 3D-Objekte (Puppen, Requisiten etc.)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *********************************************************/

if(!$myauth->checkAuth()) fehler(108);

// Überschrift
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich', d_feld::getString(4032)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'i_3dobj'),
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
                $i3d = new Obj3d;        // Formular anfordern
                $i3d->add(false);
            else :
                $i3d = unserialize($myauth->getAuthData('obj'));
                $i3d->add(true);       // Auswertezweig
                $i3d->view();
            endif;
            break;

        case "edit" :
            if (isset($_POST['form'])) :
                $i3d = new Obj3d($_POST['id']);
                $i3d->edit(false); // Formular anfordern
            else :                 // Auswertezweig
                $i3d = unserialize($myauth->getAuthData('obj'));
                $i3d->edit(true);
                $erg = $i3d->set();
                if ($erg) fehler($erg);
                $i3d->view();
            endif;
        break;	// Ende edit

        case "search" :
            if (isset($_POST['sstring'])) :
                $str = $_POST['sstring'];
                $myauth->setAuthData('search', $str);
                $list = Obj3d::search($str);
                if (!empty($list) AND is_array($list)) :
                    // Ausgabe
                    $bg = 1;
                    foreach(($list) as $nr) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        $item = new Obj3d($nr);
                        $item->view();
                    endforeach;
                else :
                    warng(102); // kein Erg.
                endif;
            endif;
        break;

        case "del" :
            $i3d = new Obj3d($_POST['id']);
            $erg = $i3d->del();
            if(empty($erg)) erfolg(); else fehler($erg);
        break;

        case 'view' :
            $i3d = new Obj3d((int)$_REQUEST['id']);
            $i3d->view();
        break;

        case 'addImage' :
            /** --- BAUSTELLE --- **/
            $img = new Bild();
            $img->add();
        break;

    endswitch;
endif;
?>