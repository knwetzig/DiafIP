<?php namespace DiafIP {
    global $marty, $myauth;
    /**
     * Eventhandler für Änderung des eigenen Passwortes
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/configs/adm_self.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @requirement PHP Version >= 5.4
     */

    if (!$myauth->getAuth()) {
        feedback(108, 'error'); // Fremdaufruf
        exit();
    }

    if ($myauth->getAuthData('rechte') < 2) {
        feedback(2, 'error');
        exit(2);
    }


// Dialog anzeigen
    $data = a_display([ // name,inhalt,rechte, optional-> $label,$tooltip,valString
                        new d_feld('bereich', null, VIEW, 4015),
                        new d_feld('name', $myauth->getUsername(), VIEW, 4016),
                        new d_feld('rname', $myauth->getAuthData('realname'), VIEW, 517),
                        new d_feld('pwd', null, VIEW, 4017),
                        new d_feld('pwd2', null, VIEW, 4018),
                        new d_feld('submit', 'speichern', VIEW, 4019)
                      ]);
    $marty->assign('dialog', $data);
    $marty->display('adm_setpass.tpl');

    if (isset($_POST['submit']) AND $_POST['submit'] === "speichern") {
        if ($_POST['password'] != "" AND ($_POST['password'] !== $_POST['password2'])) {
            feedback(110, 'error');
            exit();
        } else {
            $data = $myauth->changePassword($myauth->getUsername(), $_POST['password']);
            IsDbError($data);
            feedback(3, 'erfolg');
        }
    }
    unset($_POST);
}
