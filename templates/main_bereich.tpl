{**************************************************************
Smarty-Template: Bereichsseite für alle "Sektionen"

$Rev: 93 $
$Author: knwetzig $
$Date: 2014-08-16 16:27:21 +0200 (Sat, 16. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/main_bereich.tpl $

***** (c) DIAF e.V. *******************************************}

<div id='bereich'>
    {$dialog['bereich'][1]}

    <div id='bearbzeile'>
        {if isset($dialog['sstring'])}
        <div id='left'>
            <form action='{$dlg['phpself']}' method='post'>
                <input title="{$dialog['sstring'][2]}"
                type='text'
                name='sstring'
                value="{$dialog['sstring'][1]}"
                onfocus="if(this.value=='{$dialog['sstring'][1]}'){literal}{this.value='';}{/literal}"
                />
                <input
                type='hidden'
                name='sektion'
                value='{$sektion}'
                />
                <input
                type='hidden'
                name='aktion'
                value='search'
                />
            </form>
        </div>
        {/if}

        <div id='bearbbtn'>
        {if isset($dialog['add'])}
            <form action='{$dlg['phpself']}' method='post'>
            {if isset($dialog['extra'])}
                    <button
                        class='small'
                        name='aktion'
                        value='extra'
                        onmouseover="return overlib('{$dialog['extra'][3]}', DELAY, 1000);"
                        onmouseout="return nd();"
                    >{$dialog['extra'][1]}</button>
            {/if}
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
        {/if}
        </div>
    </div>
</div>