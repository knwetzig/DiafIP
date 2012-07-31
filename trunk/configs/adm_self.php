<?php
/*****************************************************************************
Eventhandler für Änderung des eigenen Passwortes

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

if (!$myauth->getAuth()) {
    fehler(108);
    die();           // Fremdaufruf!
}

if($myauth->getAuthData('rechte') < 2) {
    fehler(2);
    die();
}

if(isset($_POST['submit']) AND $_POST['submit'] === "speichern") {
    /* Möglichkeit den Realnamen zu ändern
    if (!preg_match(NAMEN, trim($_POST['rname']))) {
        fehler(109);
    } else {
        ggf. eine Emailadresse überprüfung siehe dazu http://www.php-faq.de/ > Regex
        $myauth->setAuthData('realname', $_POST['rname']);
        $Benutzer->setEmail(($_POST['email']));
    */
        if (normtext($_POST['password']) != "" AND (normtext($_POST['password']) !== normtext($_POST['password2']))) {
            fehler(110);
        } else {
            $data = $myauth->changePassword($myauth->getUsername(), $_POST['password']);
            if(PEAR::isError($data)) {
                die($data);
            } else _v(d_feld::getString(3));
        }
    unset($_POST);
} else {
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
}
?>