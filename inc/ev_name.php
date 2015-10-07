<?php namespace DiafIP {
    /**
     * Eventhandler für Aktionen der Namensverwaltung
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/inc/ev_name.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     **************************************************************/

    global $marty, $myauth, $str;
    if (!$myauth->checkAuth()) feedback(108, 'error');

    // Kopf
    $data = [new d_feld('bereich', $str->getStr(4012)),
             new d_feld('sektion', 'N')];
    $marty->assign('dialog', a_display($data));
    $marty->assign('darkBG', 0);
    $marty->display('main_bereich.tpl');

    if (isset($_REQUEST['aktion']) ? $_REQUEST['aktion'] : '') :
        // switch:aktion => add | edit | search | del | view
        switch ($_REQUEST['aktion']) :
            case 'extra':
            case 'add':
                if (isset($_POST['form'])) :
                    // neues Formular
                    $n = new PName;
                    $n->add(false);
                else :
                    $n = unserialize($myauth->getAuthData('obj'));
                    // Formular auswerten
                    $n->add(true);
                    $n->display('pers_dat.tpl');
                endif;
                break; // Ende --addName--

            case "edit" :
                if (isset($_POST['form'])) :
                    $n = new PName($_POST['id']);
                    // Daten einlesen und Formular anzeigen
                    $n->edit(false);
                else :
                    $n = unserialize($myauth->getAuthData('obj'));
                    $n->edit(true);
                    $erg = $n->save();
                    if ($erg) feedback($erg, 'error');
                    $n->view();
                endif;
                break; // Ende --edit --

            case "del" :
                $n = new PName($_POST['id']);
                $n->del();
                break;

            case "view" :
                $n = new PName($_REQUEST['id']);
                $n->display('pers_dat.tpl');
                break; // Endview
        endswitch;
    endif;
}
// aus iwelchen Gründen wurde keine 'aktion' ausgelöst?