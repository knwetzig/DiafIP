<?php /****************************************************
Eventhandler für Aktionen der Personenverwaltung

$Rev$
$Author$
$Date: 2012-08-07 16:26:19 +0200 (#$
$URL$

ToDo:   Die Methode searchPerson in der Klassenbibliothek funktioniert nicht
        wie gewünscht. Eine Überarbeitung der SQL-Abfrage ist erforderlich.

        Löschanfrage via Datenfeld eintragen (Papierkorb). löschen als Cron-Job
        nach 4 Wochen

***** (c) DIAF e.V. *******************************************/

if(!$myauth->checkAuth()) {
    fehler(108);
    die();
}

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

if (isset($_POST['aktion'])?$_POST['aktion']:'') {
    $smarty->assign('aktion', $_POST['aktion']);

    // switch:action => add | edit | search | del | view
    switch($_POST['aktion']) :
        case "add":
            if(isset($_POST['form'])) {
                // neues Formular
                $np = new Person;
                $np->newPerson(false);
            } else {
                $np = unserialize($myauth->getAuthData('obj'));
                // Formular auswerten
                $np->newPerson(true);
                $np->view();
            }
        break; // Ende --add--

        case "edit" :
            if (isset($_POST['form'])) {
                $ePer = new Person;
                // Daten einlesen und Formular anzeigen
                $ePer->getPerson($_POST['pid']);
                $ePer->editPerson(false);
            } else {
                $ePer = unserialize($myauth->getAuthData('obj'));
                $ePer->editPerson(true);
                if($ePer->setPerson()) {
                    fehler(1);
                    die();
                }
                $ePer->getPerson($ePer->id);
                $ePer->view();
            }
        break; // Ende --edit --

        case "search" :
            if (isset($_POST['sstring'])) {
            /* Suchanfrage musste entschärft werden
                if (!preg_match('/'.NAMEN.'|[*]/',$_POST['sstring'])) {
                    fehler(d_feld::getString(101));
                    break;
                } else {
            */
                $myauth->setAuthData('search', normtext($_POST['sstring']));
                $plist = Person::searchPerson($myauth->getAuthData('search'));
            // }
            if (!empty($plist) AND is_array($plist)) {
                // Ausgabe
                $bg = 1;
                foreach(($plist) as $nr) {
                    ++$bg; $smarty->assign('darkBG', $bg % 2);
                    $pers = new Person($nr);
                    $pers->view();
                }
            } else {
                fehler(102); // kein Ergebnis
            }
        }
        break; // Ende --search--

        case "del" :
            /* Voraussetzung: Überprüfung aller Abhängigkeiten. Dann kann erst
                gelöscht werden. Im Moment noch nicht implementiert */
                Person::delPerson($_POST['pid']);
        break; // del

        case "view" :
            $pers = new Person((int)$_POST['pid']);
            $pers->view();
        break;  // Endview
    endswitch;
}  // aus iwelchen Gründen wurde keine 'aktion' ausgelöst?
?>