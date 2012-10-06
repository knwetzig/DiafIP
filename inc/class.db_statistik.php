<?php
/**************************************************************
    Statistikauswertung der Datenbank

$Rev$
$Author$
$Date: 2012-08-09 19:41:46 +0#$
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
        $sql = 'SELECT COUNT(*) FROM f_main;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4000)] = $data['count'];

        $sql = 'SELECT COUNT(*) FROM ONLY f_film;';
        $data = $db->extended->getRow($sql, 'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4001)] = $data['count'];

        // Anzahl Personendaten
        $sql = 'SELECT COUNT(*) FROM p_person;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4003)] = $data['count'];
/*
        // Anzahl der filmkopien
        $sql = 'SELECT COUNT(*) FROM i_fkopie;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4002)] = $data['count'];
*/
    }

    function view() {
        return $this->statistic;
    }
}
?>