<?php namespace DiafIP {
    /**
     * Eventhandler für Aktionen der 3D-Objekte (Puppen, Requisiten etc.)
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/inc/ev_item_3dobj.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     */

    if (!$myauth->checkAuth()) feedback(108, 'error');

// Überschrift
    $data = a_display([ // name,inhalt,rechte, optional-> $label,$tooltip,valString
                        new d_feld('bereich', $str->getStr(4032)),
                        new d_feld('sstring', $str->getStr(4011)),
                        new d_feld('sektion', 'i_3dobj'),
                        new d_feld('add', true, RE_EDIT, null)]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_POST['aktion']) ? $_POST['aktion'] : '') :
        $marty->assign('aktion', $_POST['aktion']);

        switch (isset($_POST['aktion']) ? $_POST['aktion'] : '') :
            case "add":
                if (isset($_POST['form'])) :
                    $i3d = new Obj3d; // Formular anfordern
                    $i3d->add(false);
                else :
                    $i3d = unserialize($myauth->getAuthData('obj'));
                    $i3d->add(true); // Auswertezweig
                    $i3d->view();
                endif;
                break;

            case "edit" :
                if (isset($_POST['form'])) :
                    $i3d = new Obj3d($_POST['id']);
                    $i3d->edit(false); // Formular anfordern
                else : // Auswertezweig
                    $i3d = unserialize($myauth->getAuthData('obj'));
                    $i3d->edit(true);
                    $erg = $i3d->set();
                    if ($erg) feedback($erg, 'error');
                    $i3d->view();
                endif;
                break; // Ende edit

            case "search" :
                if (isset($_POST['sstring'])) :
                    $str = $_POST['sstring'];
                    $myauth->setAuthData('search', $str);
                    $list = Obj3d::search($str);
                    if (!empty($list) AND is_array($list)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($list) as $nr) :
                            ++$bg;
                            $marty->assign('darkBG', $bg % 2);
                            $item = new Obj3d($nr);
                            $item->sview();
                        endforeach;
                    else :
                        feedback(102, 'hinw'); // kein Erg.
                    endif;
                endif;
                break;

            case "del" :
                $i3d = new Obj3d($_POST['id']);
                $erg = $i3d->del();
                if (empty($erg)) feedback(3, 'erfolg'); else feedback($erg, 'error');
                break;

            case 'view' :
                $i3d = new Obj3d((int)$_REQUEST['id']);
                $i3d->view();
                break;

            case 'addImage' :
                /** --- BAUSTELLE --- */
                $img = new Bild();
                $img->add();

        endswitch;
    endif;
}