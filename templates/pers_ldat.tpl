{**************************************************************
Smarty-Template für die Ansicht von Personendaten (Liste)

$Rev: 93 $
$Author: knwetzig $
$Date: 2014-08-16 16:27:21 +0200 (Sat, 16. Aug 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/pers_ldat.tpl $

    call:   person2.class.php
    class:  Person
    proc:   sview
    param:  dialog[???][0] feldname
                       [1] inhalt (evt. weitere arrays)
                       [2] label
                       [3] Tooltip (soweit vorhanden)

***** (c) DIAF e.V. *******************************************}

<div class="list-item list-item-person {if $darkBG}darkBG{/if}">

    <div id='bearbzeile'>
        {* --Name-- *}
        <div id='left' class="fett">
        {if !empty($dialog['pname'][1])}{$dialog['pname'][1]}{/if}
        {if !empty($dialog['aliases'][1])}
            <span class="alias">
                ({foreach $dialog['aliases'][1] as $alias}{$alias}{if $alias@last}){else},&nbsp;{/if}
                {/foreach}
            </span>
        {/if}
        </div>

        {* --Bearbeitungssymbole-- *}
        <form id='bearbbtn' action='{$dlg['phpself']}' method="post">
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
                    value="del"><img src="images/del.png" /></button>
            {/if}

            <input type="hidden" name="form" value="true" />
            <input type="hidden" name="sektion" value="{$dialog['bereich'][1]}" />
            <input type="hidden" name="id" value="{$dialog['id'][1]}" />
        </form>
    </div>

    {* --Geburtstagszeile-- *}
    {*    <img id='portrait_klein' src="images/platzhalter.png" alt="bild" />*}
    {if !empty($dialog['gtag'][1]) OR !empty($dialog['gort'][1])}
    <div id='einzug'>
        {if !empty($dialog['gtag'][2])}{$dialog['gtag'][2]}:{/if}
        {if !empty($dialog['gtag'][1])}{$dialog['gtag'][1]}{/if}
        {if !empty($dialog['gort'][1])}
            &nbsp;{$dialog['gort'][2]}&nbsp;{$dialog['gort'][1]['ort']}
            &nbsp;({$dialog['gort'][1]['land']},&nbsp;{$dialog['gort'][1]['bland']})
        {/if}
    </div>
    {/if}

    {* --Todeszeile-- *}
    {if !empty($dialog['ttag'][1]) OR !empty($dialog['tort'][1])}
    <div id='einzug'>
        {if !empty($dialog['ttag'][2])}{$dialog['ttag'][2]}{/if}
        {if !empty($dialog['ttag'][1])}{$dialog['ttag'][1]}{/if}
        {if !empty($dialog['tort'][1])}
            &nbsp;{$dialog['tort'][2]}&nbsp;{$dialog['tort'][1]['ort']}
            &nbsp;({$dialog['tort'][1]['land']},&nbsp;{$dialog['tort'][1]['bland']})
        {/if}
    </div>
    {/if}
</div>
