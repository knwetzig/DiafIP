<?php namespace DiafIP {
    global $myauth, $marty, $str;
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
              new d_feld('sektion', 'F'),
              new d_feld('add', true, RE_EDIT, null, 4024)]);
    $marty->assign('dialog', $data);
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        $marty->assign('aktion', $_REQUEST['aktion']);

        // switch:action => add | edit | search | del | view
        switch (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : 'view') :
            case "add":
                if (isset($_POST['form'])) :
                    $motpic = new Film; // Formular anfordern
                    $motpic->add(false);
                else :
                    $motpic = unserialize($myauth->getAuthData('obj'));
                    $motpic->add(true); // Auswertezweig
                    $FilmNr = $motpic->getId();
                    unset($motionpic);
                    $fKontroll = new Film($FilmNr);
                    $fKontroll->display('figd_dat.tpl');
                endif;
                break; // Ende add

            case "edit" :
                if (isset($_POST['form'])) :
                    $motpic = new Film($_POST['id']);
                    $motpic->edit(false); // Formular anfordern
                else : // Auswertezweig
                    $motpic = unserialize($myauth->getAuthData('obj'));
                    $motpic->edit(true);
                    $motpic->save();
                    $FilmNr = $motpic->getId();
                    unset($motpic);
                    $fKontroll = new Film($FilmNr);
                    $fKontroll->display('figd_dat.tpl');
                endif;
                break; // Ende edit

            case "search" :
                if (isset($_POST['sstring'])) :
                    $suche = $_POST['sstring'];
                    $myauth->setAuthData('search', $suche);
                    $tlist = Film::search($suche);
                    if (!empty($tlist) AND is_array($tlist)) :
                        // Ausgabe
                        $bg = 1;
                        foreach (($tlist) as $nr) :
                            // Eingrenzung Filme
                            if(Entity::getBereich($nr) === 'F') :
                                ++$bg;
                                $motpic = new Film($nr);
                                $marty->assign('darkBG', $bg % 2);
                                $motpic->display('figd_ldat.tpl');
                                unset($motpic);
                            endif;
                        endforeach;
                    else :
                        feedback(102, 'hinw'); // kein Erg.
                    endif;
                endif;
                break;

            case "del" :
                $motpic = new Film(intval($_POST['id']));
                $erg  = $motpic->del();
                if (empty($erg)) feedback(3, 'erfolg'); else feedback($erg, 'warng');
                break;

            case 'view' :
                $motpic = new Film(intval($_REQUEST['id']));
                $motpic->display('figd_dat.tpl');
                break;

            case 'addCast' :
                $cast = ['fid' => intval($_POST['id']),
                         'pid' => intval($_POST['pid']),
                         'tid' => intval($_POST['tid'])];
                $motpic = new Film($_POST['id']);
                $motpic->addCast($cast);
                $marty->assign('aktion', 'edit');           // Anzeige nach Bearbeitung
                $motpic->edit(false);
                break;

            case 'delCast' :
                $cast = ['fid' => intval($_POST['id']),
                         'pid' => intval($_POST['pid']),
                         'tid' => intval($_POST['tid'])];
                $motpic = new Film($_POST['id']);
                $motpic->delCast($cast);
                $marty->assign('aktion', 'edit');
                $motpic->edit(false);
                break;

            case 'addImage' :
                /** --- BAUSTELLE
                // aufruf von figd_dialog.tpl
                $img = new Bild();
                $img->add();
                --- **/
        endswitch;
    endif;
}