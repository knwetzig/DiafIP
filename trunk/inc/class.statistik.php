<?php
/************* Statistikauswertung des Datenkerns *************

$Rev$
$Author$
$Date$
$URL$

    Author: Knut Wetzig <knwetzig@gmail.com>

**************************************************************/

class db_stat {
    public
        $statistic  = array();

    function __construct() {
        global $myauth;
        if($myauth->getAuthData('uid') != 4) $this->getStat();
    }

    function getStat() {
	   global $str, $laufzeit, $outtime;
       $db = MDB2::singleton();

/*
        // Anzahl filmogr. & bibl. Datensätze
        $sql = 'SELECT COUNT(*) FROM i_main WHERE del = false;';
        $data = $db->extended->getOne($sql,'integer');
        IsDbError($data);
        $this->statistic[$str->getStr(4000)] = $data;

        $sql = 'SELECT COUNT(*) FROM ONLY f_film WHERE del = false;';
        $data = $db->extended->getOne($sql, 'integer');
        IsDbError($data);
        $this->statistic[$str->getStr(4001)] = $data;

        // Anzahl Personendaten
        $sql = 'SELECT COUNT(*) FROM p_person2 WHERE del = false;';
        $data = $db->extended->getOne($sql,'integer');
        IsDbError($data);
        $this->statistic[str->getStr(4003)] = $data;
*/

        // Anzahl Personendaten
        $sql = 'SELECT COUNT(*) FROM entity WHERE del = false;';
        $data = $db->extended->getOne($sql,'integer');
        IsDbError($data);
        $this->statistic[$str->getStr(4039)] = $data;

        // Runtime
        $laufzeit += gettimeofday(true);
        $this->statistic[$str->getStr(580)] = sprintf('%1.6f', $laufzeit);

        // Processtime Laufzeit - Zeit die Ausgaberoutinen verschlungen haben
        if(!empty($outtime)) :
            $ptime = $laufzeit - $outtime;
            $this->statistic[$str->getStr(9)] = sprintf('%1.6f', $ptime);
        endif;
    }

    function view() {
        return $this->statistic;
    }
}
?>