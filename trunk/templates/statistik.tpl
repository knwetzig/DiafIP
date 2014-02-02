{**************************************************************
    Enthält die statistischen Angaben
    (Anzahl Datensätze / Zeitverbrauch)

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div id='status' class='note'>
    <b>{$dlg[7]}</b>
    <table>
{foreach from=$stat item=wert key=schluessel}
    <tr><td>{$schluessel}:</td><td class='re'>{$wert}</td></tr>
{/foreach}  </table><br />
    <img src='images/openbsdpower.gif' alt='powered by OpenBSD' width='100px' />
</div>