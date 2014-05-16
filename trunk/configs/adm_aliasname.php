<?php
/**************************************************************

    Eventhandler für Verwaltung von Aliasnamen

section:    admin
site:       alias

Für diese Seite ist keine Internationalisierung vorgesehen

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if(!$myauth->getAuth()) {
    feedback(108, 'error');
    exit();
}

if(!isBit($myauth->getAuthData('rechte'), SEDIT )) {
    feedback(2, 'error');
    exit(2);
}

// Überschrift
$smarty->assign('dialog', array('bereich' =>
                            array( 1 => d_feld::getString(515))));
$smarty->display('main_bereich.tpl');

    function viewSelekt($nr = null) {
        global $smarty;
        $smarty->assign('list', Alias::getAliasList());
        $data = new d_feld('alias', null, SEDIT, 515);
        $smarty->assign("dialog", $data->display());
        $smarty->display("adm_selekt.tpl");
    }


    function viewAdd() {
        global $smarty;
        $dialog = array(
                0 => array('alias', null, 'neuen&nbsp;Alias&nbsp;erstellen'),
                2 => array('name', null, 'Aliasname'),
                4 => array('notiz', null, 'Anmerkungen'),
                6 => array('aktion', 'addAlias')
            );
        $smarty->assign('dialog', $dialog);
        $smarty->display('adm_dialog.tpl');
    }


if(!empty($_POST['aktion'])) switch ($_POST['aktion']) :
    case "selekt" :
        $ali = new Alias($_POST['alias']);
        // -> edit-dialog anzeigen
        $dialog[0] = array('alias', null, 'Alias&nbsp;bearbeiten');
        $dialog[2] = array('name', $ali->name, d_feld::getString(515));
        $dialog[4] = array('notiz', $ali->notiz, d_feld::getString(514));
        $dialog[6] = array('aktion', 'edAlias');
        $smarty->assign('dialog', $dialog);
        $smarty->display('adm_dialog.tpl');
        $myauth->setAuthData('obj', serialize($ali));
        break;

    case "addAlias" :
        // Auswertung auf die klassische Art
        $ali = array();
        if($_POST['name'] !== "") $ali['name'] = $_POST['name'];
        else {
            feedback(107, 'error');
            exit();
        }

        $ali['notiz'] = $_POST['notiz'];
        $data = $db->extended->autoExecute('p_alias', $ali,
                    MDB2_AUTOQUERY_INSERT, null, array('text','text'));
        IsDbError($data);
        break;

    case "edAlias" :
        $ali = unserialize($myauth->getAuthData('obj'));
        // Auswertung und DB-Update
        if($_POST['name'] !== "") $ali->name = $_POST['name'];
        $ali->notiz = $_POST['notiz'];
        $data = $db->extended->autoExecute('p_alias', $ali,
                MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($ali->id, 'integer'), array('text','text'));
        IsDbError($data);
endswitch;

if(isset($_POST['aktion']) AND $_POST['aktion'] !== 'selekt') :
    viewSelekt();
    viewAdd();
endif;


?>