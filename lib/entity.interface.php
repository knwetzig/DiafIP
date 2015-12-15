<?php namespace DiafIP {
    /**
     * Diese Klasse stellt Grundlegende Eigenschaften und Methoden für ihre
     * Kindklassen bereit.
     */

    /**
     * Öffentliche Methoden von Entity
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP\Entity
     * @version     $Id$
     * @since       r99 Aufteilung Klasse
     */
    interface iEntity {

        /**
         * Gibt die Id des Objektes zurück
         * @return int
         */
        public function getId();

        /**
         * Testet ob es einen Datensatz mit dieser Nummer gibt
         * @param $nr
         * @return bool
         */
        static function existId($nr);

        /**
         * Test ob id/bereich in der DB existiert
         *
         * @param int $nr
         * @param string $bereich
         * @return int
         */
        static function IsInDB($nr, $bereich);

        /**
         * Holt zur ID die Bereichskennung
         *
         * @param int $nr Objekt-Id
         * @return int
         */
        static function getBereich($nr);

        /**
         * schnüffelt im Papierkorb
         *
         * @return mixed
         */
        static function getTrash();

        /**
         * ermittelt Realnamen des Bearbeiters
         *
         * @return mixed
         */
        function getBearbeiter();

        /**
         * Set/Unset Flag
         *
         * @return mixed
         */
        function setValid();

        /**
         * Validierungstest
         *
         * @return mixed
         */
        function isValid();

        /**
         * Schaltet Löschflag in DB um
         *
         * @return mixed
         */
        function del();

        /**
         * Test auf Löschflag
         *
         * @return mixed
         */
        function isDel();

        /**
         * Übergibt die Datenkollektion an Smarty
         *
         * @param $vorlage
         * @return void
         */
        function display($vorlage);
    }
}