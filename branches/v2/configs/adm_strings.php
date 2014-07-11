<?php
/********************************************************************

    Bearbeiten von Einträgen in der s_string Tabelle (Spalten en, fr)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. ************************************************/

if(!$myauth->getAuth()) {
    feedback(108, 'error');
    exit();
}

if(!isBit($myauth->getAuthData('rechte'), SEDIT )) {
    feedback(2, 'error');
    exit(2);
}

function getStrList() {
    $db = MDB2::singleton();
	$sql = 'SELECT id AS nr, de FROM s_strings ORDER BY de ASC;';
	$data = $db->extended->getAll($sql, array('integer','text'));
	IsDbError($data);
    $list = array();
    foreach($data as $val) $list[$val['nr']] = $val['de'];
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
    global $smarty;
	$smarty->assign('dialog',array('string' ,$nr , 'Text auswählen'));
    $smarty->assign('list', getStrList());
	$smarty->display('adm_selekt.tpl');
}

function viewEdit($satz) {
    global $smarty;
    $smarty->assign('dialog', a_display(array(
		new d_feld('bereich', null, null, 4013),
        new d_feld('nr', $satz['nr']),
        new d_feld('de', $satz['de'], null, 10),
        new d_feld('en', $satz['en'], null, 11),
        new d_feld('fr', $satz['fr'], null, 12),
		)));
	$smarty->display('adm_string_edit.tpl');
}

$smarty->assign('dialog', array('bereich' =>
                            array( 1 => d_feld::getString(4036))));
$smarty->display('main_bereich.tpl');
$db = MDB2::singleton();
// Auswertung edit
if(isset($_POST['aktion']) AND $_POST['aktion'] === 'edit') :
    $data = array('en' => $_POST['en'], 'fr' => $_POST['fr']);
    IsDbError($db->extended->autoExecute('s_strings', $data,
                MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($_POST['nr'], 'integer')));
endif;

// Anzeige Menüelement (selekt oder editfeld)
if(empty($_POST['aktion']) OR
    (!empty($_POST['aktion']) AND $_POST['aktion'] !== 'selekt'))
    viewSelekt(); else viewEdit(getStrings((int)$_POST['string']));
?>