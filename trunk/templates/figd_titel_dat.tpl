{**************************************************************
* Aufruf von
*   figd_class.php
*   class: Titel
*   proc:  view
*
*	param:	Object Titel --> $Titel
**************************************************************}

    <table width="100%">
    <colgroup><col width=40><col width=15%><col><col width=40></colgroup>
    <tr>
        <td colspan=3>
            <div class='fett'>{$titel->titel}</div>
        </td><td class="re">
            <form method='post'>
        <!--/td>
        <td style="text-align:right;"-->{* Bearbeitungssymbole *}
            <input type='hidden' name='form' value='true' />
{*  <button class='small' name='aktion' value='del'><img src='images/del.png alt='del' />    </button>*}
            <button class='small' name='aktion' value='edit'>
                <img src='images/edit.png' alt='edit' />
            </button>
            <input type='hidden' name='section' value='titel' />
            <input type='hidden' name='tid' value='{$titel->id}' /></form>
        </td>
    </tr> {* Ende Kopfzeile *}
{if $titel->utitel}
    <tr>
        <td>&nbsp;</td>
        <td>{$dialog[0]}:</td>
        <td>{$titel->utitel}</td>
    </tr>
{/if}
{if $titel->atitel}
    <tr>
        <td>&nbsp;</td>
        <td>{$dialog[1]}:</td>
        <td>{$titel->atitel}</td>
    </tr>
{/if}
{if $titel->stitel}
    <tr>
        <td>&nbsp;</td>
        <td>{$dialog[2]}:</td>
        <td>{$titel->stitel} ({$titel->sfolge})</td>
    </tr>
{/if}
{if $titel->inhalt}
    <tr>
        <td>&nbsp;</td>
        <td>{$dialog[3]}:</td>
        <td style='white-space:normal'>{$titel->inhalt|nl2br}</td>
    </tr>
{/if}
    </table>
</form><hr />