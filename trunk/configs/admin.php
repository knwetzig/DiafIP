<?php
/***************************************************************
Steuerdatei für den Adminbereich / Presets

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

if($myauth->getAuthData('rechte') < 2) fehler(2);

if(isset($_POST['site']) AND isset($adm_site[$_POST['site']]))
    include $adm_site[$_POST['site']];
else
    // wenn keine site gewählt wurde (Erstaufruf)
    $smarty->display('adm_menue.tpl');
?>