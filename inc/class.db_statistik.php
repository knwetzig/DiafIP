<?php
/**************************************************************
    Statistikauswertung der Datenbank

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
	global $db;

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
    }

    function view() {
        return $this->statistic;
    }
}
?>