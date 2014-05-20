<?php /****************************************************
Eventhandler für Aktionen der Personenverwaltung

$Rev$
$Author$
$Date$
$URL$

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
        new d_feld('sektion', 'person'),
        new d_feld('add', true, EDIT, null, 10001)));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') {
    $smarty->assign('aktion', $_REQUEST['aktion']);

    // switch:action => add | edit | search | del | view
    switch($_REQUEST['aktion']) :
        case "add":
            if(isset($_POST['form'])) {
                // neues Formular
                $np = new Person;
                $np->add(false);
            } else {
                $np = unserialize($myauth->getAuthData('obj'));
                // Formular auswerten
                $np->add(true);
                $np->view();
            }
        break; // Ende --add--

        case "edit" :
            if (isset($_POST['form'])) {
                $ePer = new Person($_POST['id']);
                // Daten einlesen und Formular anzeigen
                $ePer->edit(false);
            } else {
                $ePer = unserialize($myauth->getAuthData('obj'));
                $ePer->edit(true);
                $erg = $ePer->set();
                if ($erg) feedback($erg, 'error');
                $ePer->view();
            }
        break; // Ende --edit --

        case "search" :
            if (isset($_POST['sstring'])) {
            /* Suchanfrage musste entschärft werden
                if (!preg_match('/'.NAMEN.'|[*]/',$_POST['sstring'])) {
                    feedback(d_feld::getString(101), 'warng');
                    break;
                } else {
            */
                $myauth->setAuthData('search', $_POST['sstring']);
                $plist = Person::search($myauth->getAuthData('search'));
            // }
            if (!empty($plist) AND is_array($plist)) :
                // Ausgabe
                $bg = 1;
                foreach(($plist) as $nr) :
                    ++$bg; $smarty->assign('darkBG', $bg % 2);
                    $pers = new Person($nr);
                    $pers->sview();
                endforeach;
            else : feedback(102, 'hinw'); // kein Ergebnis
            endif;
        }
        break; // Ende --search--

        case "del" :
            $pers = new Person($_POST['id']);
            $pers->del();
        break;

        case "view" :
            $pers = new Person((int)$_REQUEST['id']);
            $pers->view();
        break;  // Endview
    endswitch;
}  // aus iwelchen Gründen wurde keine 'aktion' ausgelöst?
?>