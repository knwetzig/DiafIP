<?php namespace DiafIP {
    use MDB2;

    /** Statistik-Klasse */
    class db_stat {

        /**
         * Der Inhalt
         * @var array
         */
        public $statistic = [];
        protected $laufzeit, $outtime;

        /**
         * Objektinitialisierung
         *
         * @param $lz
         * @param null $oz
         */
        function __construct($lz, $oz=null) {
            global $myauth;
            $this->laufzeit = $lz;
            $this->outtime  = $oz;
            if ($myauth->getAuthData('uid') != 4) $this->getStat(); // für Gäste gibt's keine Statistik
        }


        /**
         * Holt die einzelnen Parameter
         */
        function getStat() {
            global $str;
            $db = MDB2::singleton();

            /*
            // Anzahl Filmkopien
            $sql = 'SELECT COUNT(*) FROM i_main WHERE del = false;';
            $data = $db->extended->getOne($sql,'integer');
            IsDbError($data);
            $this->statistic[$str->getStr(4000)] = $data;

            // Anzahl Objekte
            $sql  = 'SELECT COUNT(*) FROM entity WHERE del = FALSE;';
            $data = $db->extended->getOne($sql, 'integer');
            IsDbError($data);
            $this->statistic[$str->getStr(4039)] = $data;
            */

            // Anzahl Personendaten
            $sql = 'SELECT COUNT(*) FROM ONLY p_person2 WHERE del = false;';
            $data = $db->extended->getOne($sql,'integer');
            IsDbError($data);
            $this->statistic[$str->getStr(4003)] = $data;

            // Anzahl Filmdatensätze
            $sql = 'SELECT COUNT(*) FROM ONLY f_film2 WHERE del = false;';
            $data = $db->extended->getOne($sql, 'integer');
            IsDbError($data);
            $this->statistic[$str->getStr(4001)] = $data;

            // Runtime (Gesamt)
            $this->laufzeit += gettimeofday(true);
            $this->statistic[$str->getStr(580)] = sprintf('%1.3f', $this->laufzeit);

            // Prozesszeit = Laufzeit - Zeit die Ausgaberoutinen verschlungen haben
            if (!empty($this->outtime)) :
                $this->statistic[$str->getStr(9)] = sprintf('%1.3f', $this->laufzeit - $this->outtime);
            endif;
        }

        /**
         * Gibt die Ergebnisse aus
         * @return array
         */
        function view() {
            return $this->statistic;
        }
    }
}