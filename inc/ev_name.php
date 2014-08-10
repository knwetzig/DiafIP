<?php /********************************************************

    Eventhandler für Aktionen der Namensverwaltung

$Rev$
$Author$
$Date$
$URL$
**************************************************************/

if (!$myauth->checkAuth()) feedback(108, 'error');

// Überschrift
$data = array( new d_feld('bereich', d_feld::getString(4012)),
               new d_feld('sektion', 'N'));
$smarty->assign('dialog', a_display($data));
$smarty->assign('darkBG', 0);
$smarty->display('main_bereich.tpl');

if (isset($_REQUEST['aktion'])?$_REQUEST['aktion']:'') :

    // switch:aktion => add | edit | search | del | view
    switch($_REQUEST['aktion']) :
        case 'extra':
        case 'add':
            if (isset($_POST['form'])) :
                // neues Formular
                $n = new PName;
                $n->add(false);
            else :
                $n = unserialize($myauth->getAuthData('obj'));
                // Formular auswerten
                $n->add(true);
                $n->display('pers_dat.tpl');
            endif;
            break; // Ende --addName--

        case "edit" :
            if (isset($_POST['form'])) :
                $n = new PName($_POST['id']);
                // Daten einlesen und Formular anzeigen
                $n->edit(false);
            else :
                $n = unserialize($myauth->getAuthData('obj'));
                $n->edit(true);
                $erg = $n->save();
                if ($erg) feedback($erg, 'error');
                $n->view();
            endif;
            break; // Ende --edit --

        case "del" :
            $n = new PName($_POST['id']);
            $n->del();
            break;

        case "view" :
            $n = new PName($_REQUEST['id']);
            $n->display('pers_dat.tpl');
            break;  // Endview
    endswitch;
endif;
    // aus iwelchen Gründen wurde keine 'aktion' ausgelöst?
?>