<?php namespace DiafIP {
    /**
     * Eventhandler für Verwaltung von Lagerorten
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/configs/adm_lort.php $
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @requirement PHP Version >= 5.4
     *
     * ToDo: BAUSTELLE - Vorsicht hier steht nix gerade!
     **************************************************************/

    if (!$myauth->getAuth()) {
        feedback(108, 'error'); // Fremdaufruf!
        exit();
    }

    if (!isBit($myauth->getAuthData('rechte'), SEDIT)) {
        feedback(2, 'error');
        exit(2);
    }

    $marty->assign('dialog', ['bereich' => [1 => $str->getStr(472)]]);
    $marty->display('main_bereich.tpl');
    feedback('Dieser Bereich ist in der Konstruktionsphase und nicht verfügbar.', 'warng');
    exit;

    switch ($_POST['aktion']) :
        case "selekt" :
            $lo = new LOrt($_POST['lort']);
            $myauth->setAuthData('obj', serialize($lo));
            break;

        case "edLOrt" :
            $lo->edit(true);
            break;

        case "addLOrt" :
            $lo = new LOrt();
            $lo->add($_POST['lagerort']);
            erfolg();
            break;

        case "delLOrt" :
            echo 'löscht den Lagerort';
            break;

        default :
            // Ausgabe: Liste zum auswählen
            $marty->assign('list', LOrt::getLOrtList());
            $data = new d_feld('lort', null, ARCHIV, 472);
            $marty->assign("dialog", $data->display());
            $marty->display("adm_selekt.tpl");

            // Editfeld zur Neueingabe
            $dialog = [0 => ['lort', null, 'neuen&nbsp;Lagerort&nbsp;erstellen'],
                       2 => ['lagerort', null, 'Lagerort'],
                       6 => ['aktion', 'addLOrt']];
            $marty->assign('dialog', $dialog);
            $marty->display('adm_dialog.tpl');
    endswitch;
}