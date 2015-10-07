{**************************************************************
$Rev: 50 $ --> FINALVERSION
$Author: knwetzig $
$Date: 2014-05-16 15:21:27 +0200 (Fri, 16. May 2014) $
$URL: https://diafip.googlecode.com/svn/trunk/templates/adm_dialog.tpl $
***** (c) DIAF e.V. *******************************************}

<form action='{$dlg['phpself']}' method='post'><fieldset style='margin-top:25px'><legend>{$dialog[0][2]}</legend><table>
    <colgroup><col><col></colgroup>

{if isset($dialog[1])}<tr><td>$dialog[1][2]</td><td>
    {html_options name=$dialog[1][0] options=$list selected=$dialog[1][1]}
</td></tr>{/if}

{if isset($dialog[2])}<tr><td>{$dialog[2][2]}</td><td>
        <label>
            <input type="text" name="{$dialog[2][0]}" value="{$dialog[2][1]}"/>
        </label>
    </td></tr>{/if}

{if isset($dialog[3])}<tr><td>{$dialog[3][2]}</td><td>
    <input title="{$dialog[3][2]}" type="text" name="{$dialog[3][0]}" value="{$dialog[3][1]}" />
</td></tr>{/if}

{if isset($dialog[4])}<tr><td class="top">{$dialog[4][2]}</td><td>
    <textarea cols="26" rows="8" name="{$dialog[4][0]}">{$dialog[4][1]}</textarea>
</td></tr>{/if}

<tr><td colspan="2" class="re">
{if isset($dialog[5])}
    <button type="submit" name="{$dialog[5][0]}" value="{$dialog[5][1]}">
        {$dialog[5][2]}
    </button>{/if}

    <button type="submit" name="{$dialog[6][0]}" value="{$dialog[6][1]}">
        speichern
    </button>
</td></tr></table></fieldset>
    <input type="hidden" name="sektion" value="admin" />
    <input type="hidden" name="site" value="{$dialog[0][0]}" />
</form>