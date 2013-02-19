<?php
/*****************************************************************************
Eventhandler für Userverwaltung

sektion:    admin
site:       user

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if (!$myauth->getAuth()) fehler(108);
if(!(isBit($myauth->getAuthData('rechte'), ADMIN ) OR
                        isBit($myauth->getAuthData('rechte'), SU ))) fehler(2);

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

    function viewAddDialog() {              // Ausgabe: Neuen Nutzer anlegen
        global $smarty;
        $data = array();
        $data[0] = array('user', $_SERVER['PHP_SELF'], d_feld::getString(4034));
        $data[2] = array('username', null, d_feld::getString(4035));
        $data[3] = array('pwd', null, d_feld::getString(4017));
        $data[6] = array('aktion', 'addUser');
        $smarty->assign('dialog', $data);
        $smarty->display('adm_dialog.tpl');
    }

    function viewSelektDialog() {
        global $smarty, $myauth;
        $smarty->assign('list', getUserList());
        if (isset($_POST['user']) ? $seluid = $_POST['user'] : $seluid = $myauth->getAuthData('selUser'));

        $data = new d_feld('user', $seluid, null, 4016);
        $smarty->assign("dialog", $data->display());
        $smarty->display('adm_selekt.tpl');
    }

$smarty->assign('dialog',
    array('bereich' => array( 1 => d_feld::getString(4033))));
$smarty->display('main_bereich.tpl');

if(isset($_POST['aktion'])) switch ($_POST['aktion']) :
    case "selekt" :
        // Formularauswertung nach Nutzerauswahl
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
                'Depotverwaltung'
                /*
                '7',
                ...
                '15',   reserviert für ADMIN */
            ),
            'rightSel'  => bit2array($userSel['rechte']),
            'notiz'     => $userSel['notiz'],
        ));
        $smarty->display('adm_useredit.tpl');
        break;

    case "edUser" :
        $sql = 'SELECT rechte FROM S_auth WHERE uid = ?;';
        $ore = $db->extended->getOne($sql, null, $myauth->getAuthData('selUser'));
        IsDbError($ore);
        // Alle LSB löschen
        for ($i = 0; $i < 15; $i++) clearBit($ore, $i);

        if(!isset($_POST['rechte'])) :
            warng(10004);
            $_POST['rechte'] = array();
        endif;

        $data = array(
            'username'  => $_POST['username'],
            'realname'  => $_POST['realname'],
            'rechte'    => array2wert($ore, $_POST['rechte']),
            'notiz'     => $_POST['notiz'],
            'editdate'  => date('c', $_SERVER['REQUEST_TIME']),
            'editfrom'  => $myauth->getAuthData('uid')
        );

        $types = array('text','text','integer','text','date','integer');
        $data = $db->extended->autoExecute('s_auth', $data, MDB2_AUTOQUERY_UPDATE,
            'uid = '.$db->quote($myauth->getAuthData('selUser'), 'integer'), $types);
        if(!IsDbError($data)) erfolg("Die Daten wurden erfolgreich aktualisiert");
        break;

    case "addUser" :
        if($_POST['username'] != "" AND $_POST['pwd'] != "")
            $erg = $myauth->addUser($_POST['username'], $_POST['pwd']);
        if(!IsDbError($erg)) erfolg('Ein neuer Account wurde angelegt.<br />Bitte passen sie die Daten an<br />Ihre Bed&uuml;rfnisse an.');
endswitch;

// Ausgabe: Selekt-Dialog
if(isset($_POST['aktion']) AND $_POST['aktion'] !== 'selekt') :
    viewSelektDialog();
    viewAddDialog();
endif;

?>