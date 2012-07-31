<?php
class db_stat {
    public
        $statistic  = array();

    function __construct() {
        $this->getStat();
    }

    function getStat() {
	global $db;
        // Anzahl der Filme
/*
        $sql = 'SELECT COUNT(*) FROM f_film;';
        $data = $db->extended->getRow($sql,integer);
        IsDbError($data);
        $this->statistic[d_feld::getString(4001)] = $data['count'];
*/
        // Anzahl filmogr. Datensätze
        $sql = 'SELECT COUNT(*) FROM f_titel;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4000)] = $data['count'];

        // Anzahl Personendaten
        $sql = 'SELECT COUNT(*) FROM p_person;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4003)] = $data['count'];

        // Anzahl der filmkopien
        $sql = 'SELECT COUNT(*) FROM i_fkopie;';
        $data = $db->extended->getRow($sql,'integer');
        IsDbError($data);
        $this->statistic[d_feld::getString(4002)] = $data['count'];
    }

    function view() {
        return $this->statistic;
    }
}
?>