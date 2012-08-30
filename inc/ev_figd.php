<?php
/***************************************************************
    Eventhandler für Aktionen der Filmverwaltung

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if(!$myauth->checkAuth()) {
    fehler(108);
    die();
}

// Überschrift
$data = a_display(array(
    // name,inhalt,rechte, optional-> $label,$tooltip,valString
    new d_feld('bereich', d_feld::getString(4008)),
    new d_feld('sstring', d_feld::getString(4011)),
    new d_feld('sektion', 'film'),
//    new d_feld('add', true, EDIT, null, 4024)
));
$smarty->assign('dialog', $data);
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');


    switch(isset($_POST['aktion'])?$_POST['aktion']:'') {
        case "add":
            _v('Erstellen nicht implementiert');
            unset($_POST['aktion']);
        break; // Ende add

        case "edit" :
            _v('Bearbeiten nicht implementiert');
            unset($_POST['aktion']);
        break;	// Ende edit

        case "search" :
            _v('Suche noch nicht implementiert');
            unset($_POST['aktion']);
        break;

        case "del" :
            _v('Löschen nicht implementiert');
            unset($_POST['aktion']);
        break;
    } // ende SWITCH
?>