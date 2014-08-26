<?php
/**
 * Eventhandler für Verwaltung von Ortsdaten
 *
 * Für diese Seite ist keine Internationalisierung vorgesehen
 *
 * $Rev$
 * $Author$
 * $Date$
 * $URL$
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
 * @requirement PHP Version >= 5.4
 *
 * ToDo: Für das Menü das Template adm_dialog.tpl und adm_selekt verwenden. Das Programm dahin gehend umstellen und anschließend adm_orteselekt.tpl löschen.Das gleiche für das Dialog-Template in der Klassendefinition
 */

if (!$myauth->getAuth()) :
    feedback(108, 'error');
    exit();
endif;

if (!isBit($myauth->getAuthData('rechte'), SEDIT )) {
    feedback(2, 'error');
    exit(2);
}

$marty->assign('dialog', ['bereich' => [ 1 => 'Verwaltung&nbsp;der&nbsp;Ortsnamen']]);
$marty->display('main_bereich.tpl');


// Ausgabe: Ort bearbeiten
$marty->assign('olist', Ort::getOrtList());
if (isset($_POST['oid'])) $seloid = $_POST['oid'];
    else $seloid = $myauth->getAuthData('selOrt');
$marty->assign('seloid', $seloid);
$marty->display("adm_orteselekt.tpl");

if (isset($_POST['submit'])) {
    switch ($_POST['submit']) :
    case "selekt" :
        // Formularauswertung von Nutzerauswahl (impliziert bearbeiten)
        $myauth->setAuthData('selOrt', $_POST['oid']);
        $marty->assign('aktion', 'edOrt');
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
        if (!isBit($myauth->getAuthData('rechte'), DELE)) {
            feedback(2, 'error');
            exit();
        }
        $loc = new Ort($myauth->getAuthData('selOrt'));
        $loc->del();
    endswitch;
}

if (!isset($_POST['submit']) OR (isset($_POST['submit']) AND
                                    $_POST['submit'] !== "selekt")) :
    // Anzeige Formular Neuanlage
    $marty->assign('aktion', 'addOrt');
    $loc = new Ort();
    $myauth->setAuthData('obj', serialize($loc));
endif;