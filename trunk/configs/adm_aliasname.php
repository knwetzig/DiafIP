<?php
/*****************************************************************************
Eventhandler für Verwaltung von Aliasnamen

section:    admin
site:       alias

Für diese Seite ist keine Internationalisierung vorgesehen

$Rev::                              $:  Revision der letzten Übertragung
$Author::                           $:  Autor der letzten Übertragung
$Date::                             $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

if(!$myauth->getAuth()) {
    fehler(108);
    die();           // Fremdaufruf!
}

if(!isBit($myauth->getAuthData('rechte'), SEDIT )) {
    fehler(2);
    die();
}
// Überschrift
echo '<div class="bereich">Alias-/K&uuml;nstlernamen</div>';
$dialog = array(
        array('alias', null, 'neuen&nbsp;Alias&nbsp;erstellen'),
        array(null),            // Auswahlfeld
        array('name', null, 'Aliasname'),
        array(null),            // Text2
        array('notiz', null, 'Anmerkungen'),
        array('submit', 'addAlias'));

// Ausgabe: Liste zum auswählen
$smarty->assign('list', Alias::getAliasList());
$data = new d_feld('alias', null, SEDIT, 515);
$smarty->assign("dialog", $data->display());
$smarty->display("adm_selekt.tpl");
if(!isset($_POST['submit'])) {
    $smarty->assign('dialog', $dialog);
    $smarty->display('adm_dialog.tpl');
}

if(isset($_POST['submit'])) {
    switch ($_POST['submit']) :
    case "selekt" :
        // Formularauswertung von Nutzerauswahl (impliziert bearbeiten)
        $smarty->assign('aktion', 'edAlias');   // Initiator
        $ali = new Alias($_POST['alias']);
        // -> edit-dialog anzeigen
        $dialog[0][2] = 'Alias&nbsp;bearbeiten';
        $dialog[2][1] = $ali->name;
        $dialog[4][1] = $ali->notiz;
        $dialog[5][1] = $_POST['alias']?'edAlias':'addAlias';
        $smarty->assign('dialog', $dialog);
        $smarty->display('adm_dialog.tpl');
        $myauth->setAuthData('obj', serialize($ali));
        break;

    case "addAlias" :
        // Auswertung auf die klassische Art
        $ali = array();
        if($_POST['name'] !== "") $ali['name'] = normtext($_POST['name']); else {
            fehler(107);
            die();
        }
        $ali['notiz'] = normtext($_POST['notiz']);
        $data = $db->extended->autoExecute('p_alias', $ali,
                    MDB2_AUTOQUERY_INSERT, null, array('text','text'));
        IsDbError($data);
        break;

    case "edAlias" :
        $ali = unserialize($myauth->getAuthData('obj'));
        // Auswertung und DB-Update
        if($_POST['name'] !== "") $ali->name = normtext($_POST['name']);
        $ali->notiz = normtext($_POST['notiz']);
        $data = $db->extended->autoExecute('p_alias', $ali,
                MDB2_AUTOQUERY_UPDATE, 'id = '.$db->quote($ali->id, 'integer'), array('integer','text','text'));
        IsDbError($data);
        break;
    endswitch;
}
if(!isset($_POST['submit']) OR (isset($_POST['submit']) AND  $_POST['submit'] !== "selekt")) {
    // Anzeige Formular Neuanlage
    $smarty->assign('aktion', 'addAlias');
    $ali = new Alias();
    $myauth->setAuthData('obj', serialize(new Alias()));
}
unset($_POST);

?>