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
    </table><br />
{/if}
    <img src='images/openbsdpower.gif' alt='powered by OpenBSD' width='120px' />
</div>