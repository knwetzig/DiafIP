{**************************************************************
Smarty-Template -> Standard-Admin-Auswahlliste

class:
proc:
param: array(0 => Feldname, 1 => Wert, 2 => Label, 3 => Tooltip)

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:
***** (c) DIAF e.V. *******************************************}
<form method="post">
    <fieldset>
        <legend>&nbsp;{$dialog[2]}&nbsp;</legend>
            {html_options
                name=$dialog[0]
                options=$list
                selected=$dialog[1]}
        <button
            style='font-size:1.5em; width:30px'
            class="small"
            type="submit"
            name="submit"
            value="selekt"
        ><img src="images/forward.png" alt="enter" /></button>
        <input type="hidden" name="sektion" value="admin" />
        <input type="hidden" name="site" value="{$dialog[0]}" />
    </fieldset>
</form>