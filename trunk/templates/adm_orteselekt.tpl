{**************************************************************

class: s_location_class.php
proc:

$Rev:: 50                      $:  Revision der letzten Übertragung
$Author:: knwetzig             $:  Autor der letzten Übertragung
$Date:: 2014-05-16 15:21:27 +0#$:  Datum der letzten Übertragung
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_orteselekt.tpl $

ToDo:   Überarbeitung und Verwendung von adm_selekt.tpl

***** (c) DIAF e.V. *******************************************}
<form action='{$dlg['phpself']}' method='post'>
    <fieldset>
        <legend>&nbsp;Ort&nbsp;ausw&auml;hlen&nbsp;</legend>
        {html_options name=oid options=$olist selected=$seloid}
        <button
            style='font-size:1.5em; width:30px'
            class='small'
            type='submit'
            name='submit'
            value='selekt'
        ><img src="images/forward.png" alt="enter" /></button>
        <input type='hidden' name='sektion' value='admin' />
        <input type='hidden' name='site' value='orte' />
    </fieldset>
</form>