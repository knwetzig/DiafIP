<?php
/***************************************************************
Steuerdatei für den Adminbereich / Presets

$Rev::                         $:  Revision der letzten Übertragung
$Author:: Knut Wetzig          $:  Autor der letzten Übertragung
$Date:: 2012-07-31             $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************/

if($myauth->getAuthData('rechte') < 2) {
    fehler(2);
    die();
}

if(isset($_POST['site']) AND isset($adm_site[$_POST['site']])) {
    include $adm_site[$_POST['site']];
} else {
    // wenn keine site gewählt wurde (Erstaufruf)
    $smarty->display('adm_menue.tpl');
}
?>