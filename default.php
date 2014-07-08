<?php
/**************************************************************

    Die Begrüssungseite (mehrsprachig)

$Rev$
$Author$
$Date$
$URL$

**************************************************************/
    $db =& MDB2::singleton();

    $data = $db->extended->getOne(
        'SELECT '.$myauth->getAuthData('lang').' FROM s_strings WHERE id = 13;');
    IsDbError($data);

    echo $data;
?>

