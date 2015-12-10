<?php namespace DiafIP {
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */


    /**
     * Interface iPerson
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Person
     * @version     $Id$
     * @since       r99 Trennung von der alten Class
     * @requirement PHP Version >= 5.4
     *
     */
    interface iPerson extends iPName {

        /**
         * gibt ein Array der Namen zurück unter der diese Person noch bekannt ist.
         *
         * @return array|null
         */
        function getAliases();
    }
}