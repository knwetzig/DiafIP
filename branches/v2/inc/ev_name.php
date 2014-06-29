<?php /****************************************************
Eventhandler für Aktionen der Personenverwaltung

$Rev: 50 $
$Author: knwetzig $
$Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
$URL: https://diafip.googlecode.com/svn/branches/v2/inc/ev_pers.php $

ToDo:   Die Methode search in der Klassenbibliothek funktioniert nicht
        wie gewünscht. Eine Überarbeitung der SQL-Abfrage ist erforderlich.

        Löschanfrage via Datenfeld eintragen (Papierkorb). löschen als Cron-Job
        nach 4 Wochen

***** (c) DIAF e.V. *******************************************/

if(!$myauth->checkAuth()) feedback(108, 'error');

// Überschrift
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich', d_feld::getString(4012)),
        new d_feld('sstring', d_feld::getString(4011)),
        new d_feld('sektion', 'N'),
        new d_feld('extra', '<img src="images/addName.png" alt="addname" />', EDIT, null, 10011)   // PName::add
    ));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') {
    $smarty->assign('aktion', $_REQUEST['aktion']);

    // switch:aktion => add | edit | search | del | view
    switch($_REQUEST['aktion']) :
        case "extra":
            if(isset($_POST['form'])) :
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

        case "add":
            if(isset($_POST['form'])) :
                // neues Formular
                $n = new Person;
                $n->add(false);
            else :
                $n = unserialize($myauth->getAuthData('obj'));
                // Formular auswerten
                $n->add(true);
                $n->view();
            endif;
            break; // Ende --addPerson--

        case "edit" :
            if (isset($_POST['form'])) :
                $n = new PName($_POST['id']);
                // Daten einlesen und Formular anzeigen
                $n->edit(false);
            else :
                $n = unserialize($myauth->getAuthData('obj'));
                $n->edit(true);
                $erg = $n->set();
                if ($erg) feedback($erg, 'error');
                $n->view();
            endif;
            break; // Ende --edit --

        case "del" :
            $pers = new PName($_POST['id']);
            $pers->del();
            break;

        case "view" :
            $pers = new PName($_REQUEST['id']);
            $pers->display('pers_dat.tpl');
            break;  // Endview
    endswitch;
}  // aus iwelchen Gründen wurde keine 'aktion' ausgelöst?
?>