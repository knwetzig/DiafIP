<?php namespace DiafIP {
    /**
     * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Film
     * @version     $Id$
     * @since       r99 Abtrennung von Klasse
     */

    interface iFibiMain extends iEntity {
        public static function getSTitelList();
        public static function getTitelList();
        public function getTitel();
        public static function addSerTitel($titel, $descr);
        public static function editSerTitel($nr, $status = null);
        public static function delSerTitel($nr);
        public function addCast($p, $t);
        public function delCast($p, $t);
        static public function search($s);
    }
}