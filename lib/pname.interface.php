<?php namespace DiafIP {
    /**
     * Klassenbibliotheken für Personen und Aliasnamen
     *
     * Dazu gehören eine Klasse für Namen und eine Klasse für Personen inclusive der Interfaces
     */


    /**
     * Interface iPName
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Person
     * @version     $Id$
     * @since       r99
     * @requirement PHP Version >= 5.4
     */
    interface iPName extends iEntity {

        /**
         * Erstellt ....
         *
         * @return mixed
         */
        static function getUnusedAliasNameList();

        /**
         * Ermittelt die Person zum Aliasnamen
         *
         * @return mixed
         */
        function getPerson();

        /**
         * Listet alle unbenutzten Aliasnamen (nicht Personen)
         *
         * @return mixed
         */
        function getName();

        /**
         * Testet ob dieser Namenseintrag in Tabelle p_namen oder p_person existiert
         * @param $vname
         * @param $nname
         * @return bool
         */
        static function getIdFromName($nname, $vname = null);

        /**
         * liefert die ID's+Bereich des Suchmusters
         *
         * @param string $s
         */
        static function search($s);

        /**
         * Legt ein neues Objekt an
         *
         * @param bool $status
         */
        function add($status = null);

        /**
         * Bearbeitet das Objekt
         *
         * @param bool $status
         */
        function edit($status = null);

        /** speichert das bearbeitete Objekt */
        function save();

        /** Liefert die Daten für die Ausgabe */
        function view();
    }
}