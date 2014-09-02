{**************************************************************

    Standard-Admin-Auswahlliste

$dialog[0] => Feldname, 1 => Wert, 2 => Label, 3 => Tooltip)

$Rev: 50 $
$Author: knwetzig $
$Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_selekt.tpl $

***** (c) DIAF e.V. *******************************************}

<form action='{$dlg['phpself']}' method="post"><fieldset>
    <legend>&nbsp;{$dialog[2]}&nbsp;</legend>
        {html_options name=$dialog[0] options=$list selected=$dialog[1]}
    <button class="small" style="width:50px;" " type="submit" name="aktion" value="selekt">
        <img src="images/forward.png" alt="enter" />
    </button>
    <input type="hidden" name="sektion" value="admin" />
    <input type="hidden" name="site" value="{$dialog[0]}" />
</fieldset></form>