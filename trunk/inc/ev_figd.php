<?php namespace DiafIP {
    /**
     *
     * Eventhandler für Aktionen der Filmverwaltung
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/inc/ev_figd.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     *
     */

    if (!$myauth->checkAuth()) :
        feedback(108, 'error');
        exit;
    endif;

// Kopfbereich
    $data = a_display([
                          // name,inhalt,rechte, optional-> $label,$tooltip,valString
                          new d_feld('bereich', $str->getStr(4008)),
                          new d_feld('sstring', $str->getStr(4011)),
                          new d_feld('sektion', 'film'),
                          new d_feld('add', true, EDIT, null, 4024)]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        $marty->assign('aktion', $_REQUEST['aktion']);

// switch:action => add | edit | search | del | view
        switch (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : 'view') :
            case "add":
                if (isset($_POST['form'])) {
                    $film = new Film; // Formular anfordern
                    $film->add(false);
                } else {
                    $film = unserialize($myauth->getAuthData('obj'));
                    $film->add(true); // Auswertezweig
                    $film->view();
                }
                break; // Ende add

            case "edit" :
                if (isset($_POST['form'])) {
                    $film = new Film($_POST['id']);
                    $film->edit(false); // Formular anfordern
                } else { // Auswertezweig
                    $film = unserialize($myauth->getAuthData('obj'));
                    $film->edit(true);
                    $erg = $film->set();
                    if ($erg) feedback($erg, 'error');
                    $film->view();
                }
                break; // Ende edit

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
                            ++$bg;
                            $marty->assign('darkBG', $bg % 2);
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
                $erg  = $film->del();
                if (empty($erg)) feedback(3, 'erfolg'); else feedback($erg, 'warng');
                break;

            case 'view' :
                $film = new Film((int)$_REQUEST['id']);
                $film->display('blabla_film.tpl');
                break;

            case 'addCast' :
                $film = new Film($_POST['id']);
                $film->addCast($_POST['pid'], $_POST['tid']);
                $marty->assign('aktion', 'edit');
                $film->edit(false);
                break;

            case 'delCast' :
                $film = new Film($_POST['id']);
                $film->delCast($_POST['pid'], $_POST['tid']);
                $marty->assign('aktion', 'edit');
                $film->edit(false);
                break;

            case 'addImage' :
                /** --- BAUSTELLE --- **/
                // aufruf von figd_dialog.tpl
                $img = new Bild();
                $img->add();

        endswitch;
    endif;
}