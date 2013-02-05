<?php
/*****************************************************************************
Eventhandler für Verwaltung von Lagerorten

    section:    admin
    site:       lort

    $Rev$
    $Author$
    $Date$
    $URL$

***** (c) DIAF e.V. *******************************************/

if(!$myauth->getAuth()) fehler(108);          // Fremdaufruf!
if(!isBit($myauth->getAuthData('rechte'), ARCHIV)) fehler(2);

$smarty->assign('dialog', array('bereich' => array( 1 => 'Lagerort')));
$smarty->display('main_bereich.tpl');

// Ausgabe: Liste zum auswählen
$smarty->assign('list', LOrt::getLOrtList());
$data = new d_feld('Lagerort', null, ARCHIV, 472);
$smarty->assign("dialog", $data->display());
$smarty->display("adm_selekt.tpl");

// Editfeld
$dialog = array(
        0 => array('lort', null, 'neuen&nbsp;Lagerort&nbsp;erstellen'),
        2 => array('name', null, 'Lagerort'),
        6 => array('submit', 'addLort')
    );

if(!isset($_POST['submit'])) :
    $smarty->assign('dialog', $dialog);
    $smarty->display('adm_dialog.tpl');
endif;

_v($_POST);

if(isset($_POST['submit'])) :
    switch ($_POST['submit']) :
    case "selekt" :
        // Formularauswertung von Nutzerauswahl (impliziert bearbeiten)
        $smarty->assign('aktion', 'edAlias');   // Initiator
        $ali = new Alias($_POST['alias']);

        $myauth->setAuthData('obj', serialize($lort));
        break;

    case "addLort" :

        break;

    case "edLort" :

        break;
    endswitch;
endif;

if(!isset($_POST['submit']) OR (isset($_POST['submit']) AND  $_POST['submit'] !== "selekt")) {
    // Anzeige Formular Neuanlage
    $smarty->assign('aktion', 'addLort');
    $lort = new LOrt();
    $myauth->setAuthData('obj', serialize(new LOrt()));
}
unset($_POST);
?>