<?php
/**************************************************************
Eventhandler für Verwaltung von Lagerorten

    section:    admin
    site:       lort

    $Rev$
    $Author$
    $Date$
    $URL$

---------->>> BAUSTELLE <<<-------------------!!!

***** (c) DIAF e.V. *******************************************/

if (!$myauth->getAuth()) {
    feedback(108, 'error');             // Fremdaufruf!
    exit();
}

if (!isBit($myauth->getAuthData('rechte'), SEDIT )) {
    feedback(2, 'error');
    exit(2);
}

$smarty->assign('dialog', array('bereich' =>
                            array( 1 => d_feld::getString(472))));
$smarty->display('main_bereich.tpl');
feedback('Dieser Bereich ist in der Konstruktionsphase und nicht verfügbar.',
    'warng');
exit;

switch ($_POST['aktion']) :
    case "selekt" :
        $lo = new LOrt($_POST['lort']);
        $myauth->setAuthData('obj', serialize($lo));
        break;

    case "edLOrt" :
        $lo->edit(TRUE);
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
        $smarty->assign('list', LOrt::getLOrtList());
        $data = new d_feld('lort', null, ARCHIV, 472);
        $smarty->assign("dialog", $data->display());
        $smarty->display("adm_selekt.tpl");

        // Editfeld zur Neueingabe
        $dialog = array(
                0 => array('lort', null, 'neuen&nbsp;Lagerort&nbsp;erstellen'),
                2 => array('lagerort', null, 'Lagerort'),
                6 => array('aktion', 'addLOrt')
            );
        $smarty->assign('dialog', $dialog);
        $smarty->display('adm_dialog.tpl');
endswitch;
?>