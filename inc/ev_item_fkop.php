<?php namespace DiafIP {
    /**
     * Eventhandler für Aktionen der Filmkopien (Film + Medien)
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/inc/ev_item_fkop.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     */

    if (!$myauth->checkAuth()) feedback(108, 'error');

// Überschrift
    $data = a_display([
                          // name,inhalt,rechte, optional-> $label,$tooltip,valString
                          new d_feld('bereich', $str->getStr(4038)),
                          new d_feld('sstring', $str->getStr(4011)),
                          new d_feld('sektion', 'i_fkop'),
                          new d_feld('add', true, RE_EDIT, null)
                      ]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

// --- Verteiler ---
    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        $marty->assign('aktion', $_REQUEST['aktion']);

        switch (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
            case "add":
                if (isset($_POST['form'])) :
                    $ifk = new FilmKopie; // Formular anfordern
                    $ifk->add(false);
                else :
                    $ifk = unserialize($myauth->getAuthData('obj'));
                    $ifk->add(true); // Auswertezweig
                    $ifk->view();
                endif;
                break;

            case "edit" :
                if (isset($_POST['form'])) :
                    $ifk = new FilmKopie($_POST['id']);
                    $ifk->edit(false); // Formular anfordern
                else : // Auswertezweig
                    $ifk = unserialize($myauth->getAuthData('obj'));
                    $ifk->edit(true);
                    $erg = $ifk->set();
                    if ($erg) feedback($erg, 'error');
                    $ifk->view();
                endif;
                break; // Ende edit

            case "search" :
                if (isset($_POST['sstring'])) :
                    $str = $_POST['sstring'];
                    $myauth->setAuthData('search', $str);
                    $list = FilmKopie::search($str);
                    if (!empty($list) AND is_array($list)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($list) as $nr) :
                            ++$bg;
                            $marty->assign('darkBG', $bg % 2);
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

        endswitch;
    endif;
}