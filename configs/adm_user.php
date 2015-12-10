<?php namespace DiafIP {
    use MDB2;
    global $myauth, $marty, $str;
    /**
     * Eventhandler für Userverwaltung
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/configs/adm_user.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @requirement PHP Version >= 5.4
     */

    if (!$myauth->getAuth()) :
        feedback(108, 'error');
        exit();
    endif;

    if (!(isBit($myauth->getAuthData('rechte'), RE_ADMIN) OR (isBit($myauth->getAuthData('rechte'), RE_SU)))) :
        feedback(2, 'error');
        exit(2);
    endif;

    function getUserList() {
    // Nutzerauswahlliste erstellen
        $db   = MDB2::singleton();
        $sql  = 'SELECT uid, realname FROM s_auth ORDER BY realname ASC;';
        $data = $db->extended->getAll($sql);
        IsDbError($data);
        $ul = [];
        foreach ($data as $wert) :
            $ul[$wert['uid']] = $wert['realname'];
        endforeach;
        return $ul;
    }

    function viewAddDialog() { // Ausgabe: Neuen Nutzer anlegen
        global $marty, $str;
        $data    = [];
        $data[0] = ['user', $_SERVER['PHP_SELF'], $str->getStr(4034)];
        $data[2] = ['username', null, $str->getStr(4035)];
        $data[3] = ['pwd', null, $str->getStr(4017)];
        $data[6] = ['aktion', 'addUser'];
        $marty->assign('dialog', $data);
        $marty->display('adm_dialog.tpl');
    }

    function viewSelektDialog() {
        global $marty, $myauth;
        $marty->assign('list', getUserList());
        if (isset($_POST['user']) ? $seluid = $_POST['user'] : $seluid = $myauth->getAuthData('selUser')) ;

        $data = new d_feld('user', $seluid, null, 4016);
        $marty->assign("dialog", $data->display());
        $marty->display('adm_selekt.tpl');
    }

    // main
    $db = MDB2::singleton();
    $marty->assign('dialog', ['bereich' => [1 => $str->getStr(4033)]]);
    $marty->display('main_bereich.tpl');

    if (isset($_POST['aktion'])) switch ($_POST['aktion']) :
        case "selekt" :
            // Formularauswertung nach Nutzerauswahl
            $myauth->setAuthData('selUser', $_POST['user']);
            $sql     = 'SELECT * FROM s_auth WHERE uid = ?;';
            $userSel = $db->extended->getRow($sql, null, $myauth->getAuthData('selUser'));
            IsDbError($userSel);
            $marty->assign('dialog', [
                'username'   => $userSel['username'],
                'realname'   => $userSel['realname'],
                'rightboxes' => ['Allgemein lesen',
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
                ],
                'rightSel'   => bit2array($userSel['rechte']),
                'notiz'      => $userSel['notiz'],
            ]);
            $marty->display('adm_useredit.tpl');
            break;

        case "edUser" :
            $sql = 'SELECT rechte FROM S_auth WHERE uid = ?;';
            $ore = $db->extended->getOne($sql, null, $myauth->getAuthData('selUser'));
            IsDbError($ore);
            // Alle LSB löschen
            for ($i = 0; $i < 15; $i++) clearBit($ore, $i);

            if (!isset($_POST['rechte'])) :
                feedback(10004, 'warng');
                $_POST['rechte'] = [];
            endif;

            $data = [
                'username' => $_POST['username'],
                'realname' => $_POST['realname'],
                'rechte'   => array2wert($ore, $_POST['rechte']),
                'notiz'    => $_POST['notiz'],
                'editdate' => date('c', $_SERVER['REQUEST_TIME']),
                'editfrom' => $myauth->getAuthData('uid')];

            $types = ['text', 'text', 'integer', 'text', 'date', 'integer'];
            $data  = $db->extended->autoExecute('s_auth', $data, MDB2_AUTOQUERY_UPDATE,
                                                'uid = ' . $db->quote($myauth->getAuthData('selUser'), 'integer'),
                                                $types);
            if (!IsDbError($data)) feedback("Die Daten wurden erfolgreich aktualisiert", 'hinw');
            break;

        case "addUser" :
            $erg = null;
            if ($_POST['username'] != "" AND $_POST['pwd'] != "") :
                $erg = $myauth->addUser($_POST['username'], $_POST['pwd']);
                IsDbError($erg);
                feedback('Ein neuer Account wurde angelegt.<br />Bitte passen sie die Daten an<br />Ihre Bed&uuml;
                rfnisse an.', 'erfolg');
            endif;
    endswitch;

    // Ausgabe: Selekt-Dialog
    if (isset($_POST['aktion']) AND $_POST['aktion'] !== 'selekt') :
        viewSelektDialog();
        viewAddDialog();
    endif;
}