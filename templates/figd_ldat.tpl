{**************************************************************

Smarty-Template für die Listenansicht von filmogr. Daten

call: figd_class.php
class: Film
proc:  sview
param: dialog[???][0] feldname
                    [1] inhalt (evt. weitere arrays)
                    [2] label
                    [3] Tooltip (soweit vorhanden)

$Rev: 75 $
$Author: knwetzig $
$Date: 2014-08-10 16:52:18 +0200 (Sun, 10. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/figd_ldat.tpl $

***** (c) DIAF e.V. *******************************************}

<div class="list-item list-item-figd {if $darkBG}darkBG{/if}">

    {* --- Name/Status/Bearbeitungssymbole --- *}
    <div id='bearbzeile'>
        {* --Titel-- *}
        <div id='left' class="fett">
            {if !empty($dialog['titel'][1])}{$dialog['titel'][1]}{/if}
        </div>

        {* --Bearbeitungssymbole-- *}
        <form id=bearbbtn action='{$dlg['phpself']}' method="post">
            <span class="note">ID:&nbsp;{$dialog['id'][1]}&nbsp;</span>

            {if isset($dialog['edit'])}
                <button
                    class={if $darkBG}"small_dk"{else}"small"{/if}
                    name="aktion"
                    onmouseover="return overlib('{$dialog['edit'][3]}',DELAY,1000);"
                    onmouseout="return nd();"
                    value="edit"><img src="images/edit.png" /></button>
            {/if}

            {if isset($dialog['del'])}
                <button
                    class={if $darkBG}"small_dk"{else}"small"{/if}
                    name="aktion"
                    onmouseover="return overlib('{$dialog['del'][3]}',DELAY,1000);"
                    onmouseout="return nd();"
                    value="del" /><img src="images/del.png" /></button>
            {/if}
            <input type="hidden" name="form" value="true" />
            <input type="hidden" name="sektion" value="{$dialog['bereich'][1]}" />
            <input type="hidden" name="id" value="{$dialog['id'][1]}" />
        </form>
    </div>

    {* --Regie-- *}
    {if !empty($dialog['regie'][1])}
    <div id="einzug">
        {$dialog['regie'][2]}:&nbsp;{foreach $dialog['regie'][1] as $wert}{$wert}<br />{/foreach}

    </div>
    {/if}

    {* --prod_jahr-- *}
    {if !empty($dialog['prod_jahr'][1])}
        <div id="einzug">
            {$dialog['prod_jahr'][2]}:&nbsp;{$dialog['prod_jahr'][1]}
        </div>
    {/if}

    {* --prodtech-- *}
    {if !empty($dialog['prodtech'][1])}
        <div id="einzug">
            {$dialog['prodtech'][2]}:&nbsp;
            {foreach from=$dialog['prodtech'][1] item=wert}
                {$wert}
                {if !$wert@last}
                    ,&nbsp;
                {/if}
            {/foreach}
        </div>
    {/if}
</div>