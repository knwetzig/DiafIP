<?php
/***************************************************************

    Das Ladeprogramm für die Hauptseite
    Hier wird nur die "sektion" Fraktion ausgewertet

$Rev$
$Author$
$Date$
$URL$

Anm.: Schreibe 'sektion' und nicht 'section' und 'aktion' AND NOT 'action'!!!
***** (c) DIAF e.V. *******************************************/

    echo "<div id='main'>";
    if(!empty($_REQUEST['id'])) :
        /* Da eine 'id angegeben wurde' wird hier zwangsweise die 'sektion'
           ermittelt und gegebenfalls überschrieben!  */
        $bereich = array(
            'person'    => 'p_person',
            'film'      => 'f_film',
            'i_planar'  => 'i_planar',
            'i_3dobj'   => 'i_3dobj',
            'i_fkop'    => 'i_fkop');

        foreach($bereich as $key => $wert) :
            $data = $db->extended->getRow(
                'SELECT COUNT(*) FROM '.$wert.' WHERE id = ?;',
                'integer', $_REQUEST['id'], 'integer');
            IsDbError($data);
            if($data['count']) :
                $_REQUEST['sektion'] = $key;
                break;
            endif;
        endforeach;
    endif;                              // Abschluß der Testreihe

    if(isset($_REQUEST['sektion']) AND isset($datei[$_REQUEST['sektion']]))
        include $datei[$_REQUEST['sektion']];
    else include 'default.php';
    echo "</div>";
?>
