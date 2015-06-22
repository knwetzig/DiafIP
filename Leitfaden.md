# Leitfaden zur Benutzung der Applikation DIAFIP #

---

Wir freuen uns sie heute begrüßen zu können.....
## Inhaltsverzeichnis ##
  * [Orte](location.md)
  * [Personen](person.md)
  * Filmografische Daten
  * Bibliografische Daten
  * Objekte
    * Flachware
    * körperliche Objekte
    * Medien (Filmkopien/Datenträger)
  * [Messageboard](pinwand.md)
  * [Einstellungen](preferences.md)
## Allgemeine Hinweise ##
Das Suchfeld im Kopfbereich kennt zwei Modis der Suche:
  * Eingabe eines Suchmusters (also ein Teil des Namens) wobei nicht zwischen Groß- und Kleinschreibung unterschieden wird. Diese Suche ist bereichssensitiv und liefert nur Objekte die nicht gelöscht wurden.
  * Eingabe der Id im Suchfeld. Ermittelt bereichsübergreifend das gewünschte Objekt und zeigt es im jeweiligen Kontext an. Es werden auch gelöschte Objekte angezeigt, die durch nochmaliges Betätigen des Löschbuttons wieder aus dem Papierkorb heraus geholt werden (undelete).
Für viele Bedienelemente gibt es so genannte Tooltips. Das sind kleine Hinweisfenster mit einem erklärendem Text. Dieser erscheint, wenn sie länger als eine Sekunde mit dem Mauszeiger über einem solchen Element verweilen.
### Hinweise zur Bearbeitung/Neuanlage ###
Datensätze können nach Anlage nicht mehr gelöscht werden. Kontrollieren sie bitte ob entprechende Datensätze vielleicht schon vorhanden sind und nur bearbeitet werden müssen.
Sie können zu den einzelnen Felder im Dialog navigieren indem sie die Tabulatortaste benutzen (vorwärts) oder Umschalt+Tabulator (rückwärts). Durch drücken der Taste 'Enter' (nicht in mehrzeiligen Textfeldern) wird der Datensatz an den Server gesendet. Gleiches erreichen sie durch betätigen des Buttons am Ende des Dialogs.

Alle mehrzeiligen Texteingabefelder verfügen über die Fähigkeit BBCode darstellen zu können. BBCode (von engl. Bulletin Board Code) ist eine an HTML angelehnte, jedoch vereinfachte Auszeichnungssprache. Implementiert sind in dieser Anwendung folgende Elemente:

| **Element** | **Auszeichnung in BBCode** |
|:------------|:---------------------------|
|Fett         |`[b]`**fett**`[/b]`         |
|Kursiv       |`[i]`_kursiv_`[/i]`         |
|Unterstrichen|`[u]`<u>unterstrichen</u>`[/u]`|
|Zitat        |`[pre]fester Zeichenabstand[/pre]`|
|Verweis      |`[url]`http://example.com `[/url]`|
|Verweis      |`[url=http://example.com]`Verweistext`[/url]`|
|Bilder       |`[img=http://example.com/bild.png][/img]`|

Die Elemente können verschachtelt werden, wobei die Reihenfolge der schließenden Tags einzuhalten ist.

Beispiel: Das ist ein `[i]`_kursiver Text, mit einem_ `[b]`**_fetten Bestandteil_**`[/b][/i]`.