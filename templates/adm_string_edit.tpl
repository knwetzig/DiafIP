{**************************************************************

Smarty-Template für die Bearbeitung/Neuanlage von filmografischen Datensätzen

$Rev: 50 $
$Author: knwetzig $
$Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_string_edit.tpl $

***** (c) DIAF e.V. *******************************************}
<form action='{$dlg['phpself']}' method='post'>
    <fieldset>
        <legend>&nbsp;{$dialog['bereich'][2]}&nbsp;</legend>
    <table>
        <colgroup><col><col></colgroup>
        <tr><td style="vertical-align:top"><!-- linke Seite -->
{* --- de --- *}
    <tr>
        <td class="re">{$dialog['de'][2]}:</td>
        <td>{$dialog['de'][1]}</td>
    </tr>

{* --- en --- *}
    <tr>
        <td class="re">{$dialog['en'][2]}:</td>
        <td><input class='lang' type='text' name='en' value="{$dialog['en'][1]}" /></td>
    </tr>

{* --- fr --- *}
    <tr>
        <td class="re">{$dialog['fr'][2]}:</td>
        <td><input class='lang' type='text' name='fr' value="{$dialog['fr'][1]}" /></td>
    </tr>
    <tr><td class='re' colspan='2'>
        <input type='submit' name='aktion' value='edit' />
    </td></tr>
</table></fieldset>
    <input type='hidden' name='nr' value='{$dialog['nr'][1]}' />
    <input type='hidden' name='sektion' value='admin' />
    <input type='hidden' name='site' value='string' />
</form>
