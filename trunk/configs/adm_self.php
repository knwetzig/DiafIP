<?php
/*****************************************************************************
Eventhandler für Änderung des eigenen Passwortes

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if (!$myauth->getAuth()) {
    feedback(108, 'error');         // Fremdaufruf
    exit();
}

if ($myauth->getAuthData('rechte') < 2 ) {
    feedback(2, 'error');
    exit(2);
}


// Dialog anzeigen
$data = a_display(array(
        // name,inhalt,rechte, optional-> $label,$tooltip,valString
        new d_feld('bereich' , null, VIEW, 4015),
        new d_feld('name', $myauth->getUsername(), VIEW, 4016),
        new d_feld('rname', $myauth->getAuthData('realname'), VIEW, 517),
        new d_feld('pwd', null, VIEW, 4017),
        new d_feld('pwd2', null, VIEW, 4018),
        new d_feld('submit', 'speichern', VIEW, 4019)
        ));
$smarty->assign('dialog', $data);
$smarty->display('adm_setpass.tpl');

if (isset($_POST['submit']) AND $_POST['submit'] === "speichern") {
    if ($_POST['password'] != "" AND ($_POST['password'] !== $_POST['password2'])) {
        feddback(110, 'error');
        exit();
    } else {
        $data = $myauth->changePassword($myauth->getUsername(), $_POST['password']);
        IsDbError($data);
        feedback(3, 'erfolg');
        }
    }
    unset($_POST);
?>