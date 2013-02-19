{**************************************************************

    Standard-Admin-Auswahlliste

$dialog[0] => Feldname, 1 => Wert, 2 => Label, 3 => Tooltip)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<form action='{$dlg[10]}' method="post"><fieldset>
    <legend>&nbsp;{$dialog[2]}&nbsp;</legend>
        {html_options name=$dialog[0] options=$list selected=$dialog[1]}
    <button class="small" style="width:50px;" " type="submit" name="aktion" value="selekt">
        <img src="images/forward.png" alt="enter" />
    </button>
    <input type="hidden" name="sektion" value="admin" />
    <input type="hidden" name="site" value="{$dialog[0]}" />
</fieldset></form>