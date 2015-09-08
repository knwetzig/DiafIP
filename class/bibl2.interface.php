<?php namespace DiafIP;
/**
 * Klassenbibliotheken für Bibliografische Daten
 *
 * $Rev: 99 $
 * $Author: knwetzig $
 * $Date: 2014-08-27 08:29:35 +0200 (Wed, 27. Aug 2014) $
 * $URL: https://diafip.googlecode.com/svn/trunk/inc/class.figd2.php $
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
 * @requirement PHP Version >= 5.4
 *
 */


interface iBiblio extends iFibiMain {
    public function add($status = null);
    public function edit($status = null);
    public function save();
}

