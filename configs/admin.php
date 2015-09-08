<?php namespace DiafIP {
    global $marty, $myauth;
    /**
     * Steuerdatei für den Adminbereich / Presets
     *
     * $Rev: 98 $
     * $Author: knwetzig $
     * $Date: 2014-08-27 00:55:16 +0200 (Wed, 27. Aug 2014) $
     * $URL: https://diafip.googlecode.com/svn/trunk/configs/admin.php $
     *
     * @author      Knut Wetzig <knwetzig@gmail.com>
     * @copyright   Deutsches Institut für Animationsfilm e.V.
     * @license     http://opensource.org/licenses/BSD-3-Clause BSD-3 License
     * @requirement PHP Version >= 5.4
     */

    if ($myauth->getAuthData('rechte') < 2) :
        feedback(2, 'error');
        exit(2);
    endif;

    if (isset($_POST['site']) AND isset($adm_site[$_POST['site']]))
        include $adm_site[$_POST['site']];
    else
        // wenn keine site gewählt wurde (Erstaufruf)
        $marty->display('adm_menue.tpl');
}