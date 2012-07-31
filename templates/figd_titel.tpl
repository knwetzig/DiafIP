{**********************************************************
   Hauptausgabeseite für die Titelverwaltung

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

ToDo:   Aktualisierung auf neues Ausgabesystem erforderlich

***** (c) DIAF e.V. *******************************************}

<div class='bereich'>{$dialog[0]}</div>
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
