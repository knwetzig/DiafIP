<?php
/**************************************************************
    Statistikauswertung des Datenkerns

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************/

class db_stat {
    public
        $statistic  = array();

    function __construct() {
        $this->getStat();
    }

    function getStat() {
	global $db, $laufzeit, $outtime;

        // Anzahl filmogr. & bibl. Datensätze
        $sql = 'SELECT COUNT(*) FROM i_main WHERE del = false;';
        $data = $db->extended->getOne($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4000)] = $data;

        $sql = 'SELECT COUNT(*) FROM ONLY f_film WHERE del = false;';
        $data = $db->extended->getOne($sql, 'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4001)] = $data;

        // Anzahl Personendaten
        $sql = 'SELECT COUNT(*) FROM p_person WHERE del = false;';
        $data = $db->extended->getOne($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4003)] = $data;

        // Runtime
        $laufzeit += gettimeofday(true);
        $this->statistic[d_feld::getString(580)] = sprintf('%f', $laufzeit);

        // Processtime Laufzeit - Zeit die Ausgaberoutinen verschlungen haben
        if (!empty($outtime)) :
            $ptime = $laufzeit - $outtime;
            $this->statistic[d_feld::getString(9)] = sprintf('%f', $ptime);
        endif;
    }

    function view() {
        return $this->statistic;
    }
}
?>