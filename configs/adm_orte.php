<?php
/*****************************************************************************
Eventhandler für Verwaltung von Ortsdaten

section:    admin
site:       orte

Für diese Seite ist keine Internationalisierung vorgesehen

$Rev$
$Author$
$Date$
$URL$

ToDo:
    Für das Menü das Template adm_dialog.tpl und adm_selekt verwenden. Das Programm
    dahin gehend umstellen und anschließend adm_orteselekt.tpl löschen.
    Das gleiche für das Dialog-Template in der Klassendefinition
***** (c) DIAF e.V. *******************************************/

if(!$myauth->getAuth()) :
    fehler(108);
    exit;           // Fremdaufruf!
endif;

if(!isBit($myauth->getAuthData('rechte'), SEDIT )) :
    fehler(2);
    exit;
endif;

$smarty->assign('dialog', array(
    'bereich' => array( 1 => 'Verwaltung&nbsp;der&nbsp;Ortsnamen')
));
$smarty->display('main_bereich.tpl');


// Ausgabe: Ort bearbeiten
$smarty->assign('olist', Ort::getOrtList());
if (isset($_POST['oid'])) $seloid = $_POST['oid'];
    else $seloid = $myauth->getAuthData('selOrt');
$smarty->assign('seloid', $seloid);
$smarty->display("adm_orteselekt.tpl");

if(isset($_POST['submit'])) {
    switch ($_POST['submit']) :
    case "selekt" :
        // Formularauswertung von Nutzerauswahl (impliziert bearbeiten)
        $myauth->setAuthData('selOrt', $_POST['oid']);
        $smarty->assign('aktion', 'edOrt');
        $loc = new Ort($myauth->getAuthData('selOrt'));
        $loc->edit(false);
        $myauth->setAuthData('obj', serialize($loc));
        break;
    case "addOrt" :
        $loc = unserialize($myauth->getAuthData('obj'));
        $loc->neu(true);
        break;
    case "edOrt" :
        $loc = unserialize($myauth->getAuthData('obj'));
        $loc->edit(true);
        $loc->set();
        break;
    case "delOrt" :
        if(!isBit($myauth->getAuthData('rechte'), DELE)) {
            fehler(2);
            die();
        }
        $loc = new Ort($myauth->getAuthData('selOrt'));
        $loc->del();
    endswitch;
}
if(!isset($_POST['submit']) OR (isset($_POST['submit']) AND  $_POST['submit'] !== "selekt")) {
    // Anzeige Formular Neuanlage
    $smarty->assign('aktion', 'addOrt');
    $loc = new Ort();
    $myauth->setAuthData('obj', serialize($loc));
}
unset($_POST);

?>