{**************************************************************
    Enthält die statistischen Angaben
    (Anzahl Datensätze / Zeitverbrauch)

$Rev$
$Author$
$Date$
$URL$

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