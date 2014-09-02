<?php namespace DiafIP {
    /**
     * Klassenbibliotheken für Filmogr.-/Bibliografische Daten
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   2014 Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @package     DiafIP
     * @version     $Id$
     * @since       r99 Abtrennung aus Klasse
     * @requirement PHP Version >= 5.4
     */

    interface iFilm extends iFibikern {
        public function add($status = null);
        public function edit($status = null);
        public function save();
    }
}