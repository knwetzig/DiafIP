{**************************************************************
Smarty-Template: Bereichsseite für alle "Sektionen"

$Rev$
$Author$
$Date$
$URL$

***** (c) DIAF e.V. *******************************************}

<div class='bereich'>{$dialog['bereich'][1]}</div>
{if isset($dialog['sstring'])}
<div id='bereichssuche'>
    <form action='{$dlg[10]}' method='post'>
        <input
        type='text'
        name='sstring'
        value="{$dialog['sstring'][1]}"
        onfocus="if(this.value=='{$dialog['sstring'][1]}'){literal}{this.value='';}{/literal}"
        />
        <input
        type="hidden"
        name="sektion"
        value="{$dialog['sektion'][1]}"
        />
        <input
        type='hidden'
        name='aktion'
        value='search'
        />
    </form>
</div>
{/if}

{if isset($dialog['add'])}
<div id='bereichssymbole'>
    <form action='{$dlg[10]}' method='post'>
        <button
            class='small'
            name='aktion'
            value='add'
            onmouseover="return overlib('{$dialog['add'][3]}', DELAY, 1000);"
            onmouseout="return nd();"
        ><img src='images/add.png' /></button>
        <input
            type='hidden'
            name='form'
            value='true'
        />
        <input
        type="hidden"
        name="sektion"
        value="{$dialog['sektion'][1]}"
        />
    </form>
</div>
{/if}
<div class='bereich_fuss'></div>