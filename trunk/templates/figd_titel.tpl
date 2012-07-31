<div class='bereich'>{$dialog[0]}</div>
{**********************************************************
*   Hauptausgabeseite für die Titelverwaltung
*
*   Autor:      Knut Wetzig
*   Copyright:  DIAF e.V.
*   Date:       11062012
*
*   ToDo:
**********************************************************}

<table width='100%'><tr><td>
    <form method='post'>
        <input type='text' name='sstring' value="{$dialog[1]}" onfocus="if(this.value=='{$dialog[1]}'){literal}{this.value='';}{/literal}" />
        <input type='hidden' name='section' value='titel' />
        <input type='hidden' name='aktion' value='search' />
    </form></td>

    <td class="re">
        <form  method='post'>
        <button class='small' name='aktion' value='add'><img src='images/add.png' alt='add' /></button>
        <input type='hidden' name='section' value='titel' />
        <input type='hidden' name='form' value='true' />
    </form></td>
</tr></table>
<hr />
