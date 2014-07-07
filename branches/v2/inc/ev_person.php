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
        new d_feld('sektion', 'P'),
        new d_feld('add', true, EDIT, null, 10001),         // Person::add
        new d_feld('extra', '<img src="images/addName.png" alt="addname" />', EDIT, null, 10011)   // PName::add
    ));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') {
    $smarty->assign('aktion', $_REQUEST['aktion']);

    // switch:aktion => add | edit | search | del | view
    switch($_REQUEST['aktion']) :
        case "add":
            if(isset($_POST['form'])) :
                // neues Formular
                $np = new Person;
                $np->add(false);
            else :
                $np = unserialize($myauth->getAuthData('obj'));
                // Formular auswerten
                $np->add(true);
                $np->display('pers_dat.tpl');
            endif;
            break; // Ende --addPerson--

        case "edit" :
            if (isset($_POST['form'])) :
                $ePer = new Person($_POST['id']);
                // Daten einlesen und Formular anzeigen
                $ePer->edit(false);
            else :
                $ePer = unserialize($myauth->getAuthData('obj'));
                $ePer->edit(true);
                $erg = $ePer->save();
                if ($erg) feedback($erg, 'error');
                $ePer->display('pers_dat.tpl');
            endif;
            break; // Ende --edit --

        case "search" :
            if (isset($_POST['sstring'])) :
                $myauth->setAuthData('search', $_POST['sstring']);

                $plist = PName::search($myauth->getAuthData('search'));
                if (!empty($plist) AND is_array($plist)) :
                    // Ausgabe
                    $bg = 1;
                    foreach(($plist) as $val) :
                        ++$bg; $smarty->assign('darkBG', $bg % 2);
                        switch($val['bereich']) :
                            case 'N' :
                                $nam = new PName($val['id']);
                                $nam->display('pers_ldat.tpl');
                                break;
                            case 'P' :
                                $pers = new Person($val['id']);
                                $pers->display('pers_ldat.tpl');
                        endswitch;
                    endforeach;
                else : feedback(102, 'hinw'); // kein Ergebnis
                endif;
            endif;
            break; // Ende --search--

        case "del" :
            $pers = new Person($_POST['id']);
            $pers->del();
            break;

        case "view" :
            $pers = new Person($_REQUEST['id']);
            $pers->display('pers_dat.tpl');
            break;  // Endview
    endswitch;
}  // aus iwelchen Gründen wurde keine 'aktion' ausgelöst?
?>