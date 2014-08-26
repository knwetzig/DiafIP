<?php
/**
 * Steuerdatei für den Adminbereich / Presets
 *
 * $Rev$
 * $Author$
 * $Date$
 * $URL$
 *
 * @author      Knut Wetzig <knwetzig@gmail.com>
 * @copyright   Deutsches Institut für Animationsfilm e.V.
 * @license     BSD-3 License http://opensource.org/licenses/BSD-3-Clause
 * @requirement PHP Version >= 5.4
 */

if ($myauth->getAuthData('rechte') < 2) {
    feedback(2, 'error');
    exit(2);
}

if (isset($_POST['site']) AND isset($adm_site[$_POST['site']]))
    include $adm_site[$_POST['site']];
else
    // wenn keine site gewählt wurde (Erstaufruf)
    $marty->display('adm_menue.tpl');
