<?php
/*****************************************************************************
Eventhandler für Userverwaltung

sektion:    admin
site:       user

$Rev::                              $:  Revision der letzten Übertragung
$Author::                           $:  Autor der letzten Übertragung
$Date::                             $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

if (!$myauth->getAuth()) {
    fehler(108);
    die();           // Fremdaufruf!
}

if(!(isBit($myauth->getAuthData('rechte'), ADMIN ) OR
        isBit($myauth->getAuthData('rechte'), SU ))) {
    fehler(2);
    die();
}

function getUserList(){
// Nutzerauswahlliste erstellen
    global $db;
    $sql = 'SELECT uid, realname FROM s_auth ORDER BY realname ASC;';
    $data = $db->extended->getAll($sql);
    IsDbError($data);
    $ul = array();
    foreach ($data as $wert) :
        $ul[$wert['uid']] = $wert['realname'];
    endforeach;
    return $ul;
}
// Ausgabe: Neuen Nutzer anlegen
$smarty->display('adm_userneu.tpl');

// Ausgabe: Benutzerverwaltung
$smarty->assign('list', getUserList());
if (isset($_POST['user']) ? $seluid = $_POST['user'] : $seluid = $myauth->getAuthData('selUser'));
$data = new d_feld('user', $seluid, null, 4016);
$smarty->assign("dialog", $data->display());
$smarty->display('adm_selekt.tpl');

if(isset($_POST['submit'])) {
    switch ($_POST['submit']) :
    case "selekt" :
        // Formularauswertung von Nutzerauswahl
        $myauth->setAuthData('selUser', $_POST['user']);
        $sql = 'SELECT * FROM s_auth WHERE uid = ?;';
        $userSel = $db->extended->getRow($sql, null, $myauth->getAuthData('selUser'));
        IsDbError($userSel);
        $smarty->assign('dialog', array(
            'username'  => $userSel['username'],
            'realname'  => $userSel['realname'],
            'rightboxes' => array(
                'Allgemein lesen',
                'Interne Daten lesen',
                'Allgemein bearbeiten',
                'Interne Daten bearbeiten',
                'Listen/Presets bearbeiten',
                'Daten l&ouml;schen',
                /*
                '6',
                '7',
                ...
                '15',   reserviert für ADMIN */
            ),
            'rightSel'  => bit2array($userSel['rechte']),
            'notiz'     => $userSel['notiz'],
        ));
        $smarty->display('adm_useredit.tpl');
        // unset($_POST);
        break;

    case "edUser" :
        $sql = 'SELECT rechte FROM S_auth WHERE uid = ?;';
        $ore = $db->extended->getCol($sql, null, $myauth->getAuthData('selUser'));
        IsDbError($ore);
        $ore = $ore[0];
        for ($i = 0; $i < 15; $i++) clearBit($ore, $i);
        if(!isset($_POST['rechte'])) :
            fehler(4);
            $_POST['rechte'] = array();
        endif;
        $data = array(
            'username'  => normtext($_POST['username']),
            'realname'  => normtext($_POST['realname']),
            'rechte'    => bitArr2wert($ore, $_POST['rechte']),
            'notiz'     => normtext($_POST['notiz']),
            'editdate'  => date('Y-m-d', $_SERVER['REQUEST_TIME']),
            'editfrom'  => $myauth->getAuthData('uid')
        );
        $types = array('text','text','integer','text','date','integer');
        $data = $db->extended->autoExecute('s_auth', $data, MDB2_AUTOQUERY_UPDATE,
            'uid = '.$db->quote($myauth->getAuthData('selUser'), 'integer'), $types);
        if(!IsDbError($data)) _v("Die Daten wurden erfolgreich aktualisiert");
        break;

    case "addUser" :
        if($_POST['username'] != "" AND $_POST['pwd'] != "")
            $erg = $myauth->addUser(normtext($_POST['username']), normtext($_POST['pwd']));
        if(!IsDbError($erg)) _v('Ein neuer Account wurde angelegt.<br />Bitte passen sie die Daten an<br />Ihre Bed&uuml;rfnisse an.');
    endswitch;
}
unset($_POST);
?>