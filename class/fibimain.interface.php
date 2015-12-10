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
        static function getSTitelList();
        static function getTitelList();
        function getTitel();
        static function addSerTitel($titel, $descr);
        static function editSerTitel($nr, $status = null);
        static function delSerTitel($nr);
        static function addCast($cast);
        static function delCast($cast);
        static function search($s);
    }
}