<?php
/**************************************************************
    Die Begrüssungseite

$Rev::                         $:  Revision der letzten Übertragung
$Author::                      $:  Autor der letzten Übertragung
$Date::                        $:  Datum der letzten Übertragung
$URL$

***** (c) DIAF e.V. *******************************************/

echo <<<'INH'
<h2>
    Willkommen auf der Testseite des<br />"Deutschen Instituts f&uuml;r Animationsfilm"
</h2>
<p>
    Hier wird die Informationsplattform des DIAF's im folgenden
<strong>DIAFIP</strong> genannt, oder Teile davon, einem Funktions- und
Leistungstest unterzogen.<br />F&uuml;r diesen Test haben sie Zugangsdaten
bekommen die einen bestimmten Satz an Privilegien beinhalten. Alternativ
k&ouml;nnen Sie sich als "gast" mit dem Passwort "gast" einloggen (nur
lesen).<br />Momentan sehen Sie die Entwicklerversion mit der
Revisionsnummer $Rev$ vom $Date$

</p>
<p><b>Hinweise:</b>
    <ul>
        <li> Alle hier eingetragenen Daten werden nach der Testphase verworfen. Man kann also nach Herzenslust experimentieren.</li>
        <li>Wenn wider Erwarten eine Fehlermeldung auftaucht, bitte den
Fehlertext (Copy-Paste) oder die Uhrzeit mit einer kurzen Beschreibung hier
posten.</li>
        <li>BB-Code ist in den Textfeldern (Notiz etc.) erlaubt. Folgende Auszeichnungen stehen zur Verf&uuml;gung: b, i, u, pre, url und img</li>
    </ul>
    Die Eingabefelder für die Suche nehmen immer einen Teilstring auf. Das heist, die Suche mit <span class='fett'>hans</span> f&uuml;hrt vielleicht zum gesuchten Titel "<span class='fett'>Hans</span> im Gl&uuml;ck", aber auch zu "Der Junge mit der Pferdegeige". Hier findet sich der Suchtext in der Beschreibung "...Tochter des Wasserk<span class='fett'>hans</span> wird die Frau ...". Eine "leere" Suche ergibt folglich den gesamten Bestand! Gross-/Kleinschreibung wird nicht unterschieden.
</p>
<p>
    W&uuml;nsche und Vorschl&auml;ge bitte (kurz) auf die Pinwand posten.
</p>
<p>
    <strong>ToDo Funktionalit&auml;t:</strong><ol>
        <li>Funktionen zur Verwaltung der Serientitel</li>
        <li style='text-decoration:line-through;'>Verwaltung für Aliasnamen</li>
        <li style='text-decoration:line-through;'>Modul zur Verwaltung von
Orten</li>
        <li style='text-decoration:line-through;'>Erstellung
Benutzerverwaltung</li>
        ...
        <li style='text-decoration:line-through;'>Eigenverwaltung (Password,
Sprache etc)</li>
    </ol>
    <strong>Aufgaben für Benutzerdesign:</strong>
    <ul>
        <li style='text-decoration:line-through;'>Implementierung der Tooltips</li>
    </ul>
</p>
INH;
?>