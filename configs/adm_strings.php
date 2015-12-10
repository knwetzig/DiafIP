<?php namespace DiafIP {
    use MDB2;
    global $marty, $myauth, $str;
    /**
    Bearbeiten von Einträgen in der s_string Tabelle (Spalten en, fr)

    $Rev: 98 $
    $Author: knwetzig $
    $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
    $URL: https://diafip.googlecode.com/svn/trunk/configs/adm_strings.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @requirement PHP Version >= 5.4
     *
     * ToDo: Integration in class String
     *
     */

    if (!$myauth->getAuth()) {
        feedback(108, 'error');
        exit();
    }

    if (!isBit($myauth->getAuthData('rechte'), RE_SEDIT )) {
        feedback(2, 'error');
        exit(2);
    }

    function getStrList() {
        $db = MDB2::singleton();
        $sql = 'SELECT id AS nr, de FROM s_strings ORDER BY de ASC;';
        $data = $db->extended->getAll($sql, ['integer','text']);
        IsDbError($data);
        $list = [];
        foreach ($data as $val) $list[$val['nr']] = $val['de'];
        return $list;
    }

    function getStrings($nr) {
        $db = MDB2::singleton();
        $data = $db->extended->getRow(
            'SELECT id AS nr, de, en, fr FROM s_strings WHERE id = ?;', null, $nr, 'integer');
        IsDbError($data);
        return $data;
    }

    function viewSelekt($nr = null) {
        global $marty;
        $marty->assign('dialog',['string' ,$nr , 'Text auswählen']);
        $marty->assign('list', getStrList());
        $marty->display('adm_selekt.tpl');
    }

    function viewEdit($satz) {
        global $marty;
        $marty->assign('dialog', a_display(
            [new d_feld('bereich', null, null, 4013),
             new d_feld('nr', $satz['nr']),
             new d_feld('de', $satz['de'], null, 10),
             new d_feld('en', $satz['en'], null, 11),
             new d_feld('fr', $satz['fr'], null, 12)]));
        $marty->display('adm_string_edit.tpl');
    }

    $marty->assign('dialog', ['bereich' =>  [1 => $str->getStr(4036)]]);
    $marty->display('main_bereich.tpl');
    $db = MDB2::singleton();
// Auswertung edit
    if (isset($_POST['aktion']) AND $_POST['aktion'] === 'edit') :
        $data = ['en' => $_POST['en'], 'fr' => $_POST['fr']];
        IsDbError($db->extended->autoExecute('s_strings', $data,
                                             MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($_POST['nr'], 'integer')));
    endif;

// Anzeige Menüelement (selekt oder editfeld)
    if (empty($_POST['aktion']) OR
        (!empty($_POST['aktion']) AND $_POST['aktion'] !== 'selekt'))
        viewSelekt(); else viewEdit(getStrings((int)$_POST['string']));
}