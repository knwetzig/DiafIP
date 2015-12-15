<?php namespace DiafIP {
    /**
     * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
     * @version $Id$
     * @requirement PHP Version >= 5.4
     */


    /**
     * BIBLIO CLASS
     */
    class Biblio extends FibiMain implements iBiblio {

        protected // --> Variablen und Konstantendefinition
            $szahl = null, //Seitenzahl
            $format = null; // Bitset für Formate: Paperback, Heft etc..

        public function __construct($nr = null) {
            parent::__construct($nr);
            // ... Konstruktor ehem. get()
        }

        public function add($status = null) {
            /**
             *          Aufgabe: Legt neuen (leeren) Datensatz an (INSERT)
             * Aufruf:
             * Return: Fehlercode
             */
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            // ...
            return null;
        }

        public function edit($status = null) {
            /**
             * Aufgabe: Ändert die Objekteigenschaften (ohne zu speichern!)
             * Aufruf: array, welches die zu ändernden Felder enthält
             * Return: Fehlercode
             */
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            // ..
            return null;
        }

        public function save() {
            /**
             * Aufgabe: schreibt die Daten in die Tabelle 'f_biblio' zurück (UPDATE)
             * Return: Fehlercode
             */
            global $myauth;
            if (!isBit($myauth->getAuthData('rechte'), RE_EDIT)) return 2;
            // ...
            return null;
        }

        public function view() {
            /**
             * Aufgabe: prepare to display
             * Aufruf:
             * Return: Fehlercode
             */
            global $myauth, $marty;
            if (!isBit($myauth->getAuthData('rechte'), RE_VIEW)) return 2;
            // ....
            return null;
        }
    } // endclass Biblio
}
