{**************************************************************

class: s_location_class.php
proc:

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:   Überarbeitung und Verwendung von adm_selekt.tpl

***** (c) DIAF e.V. *******************************************}
<form action='{$dlg[100]}' method='post'>
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