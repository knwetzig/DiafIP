{**************************************************************
    Enthält die statistischen Angaben
    (Anzahl Datensätze / Zeitverbrauch)

$Rev: 93 $
$Author: knwetzig $
$Date: 2014-08-16 16:27:21 +0200 (Sat, 16. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/statistik.tpl $

***** (c) DIAF e.V. *******************************************}

<div id='status' class='note'>
{if !empty($stat)}
    <b>{$dlg['stat']}</b>
    <table>
        {foreach $stat as $wert}
        <tr><td>{$wert@key}:</td><td class='re'>{$wert}</td></tr>
        {/foreach}
    </table>
{/if}
    <a href='http://openbsd.org' target='_new'><img src='images/puflogh200X50.gif' alt='OpenBSD' width='150px' /></a>
</div>