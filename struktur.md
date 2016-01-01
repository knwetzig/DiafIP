Übersicht über den Aufbau der Applikation
=========================================
Bereitstellung
--------------
###Ladereihenfolge:
__index.php__   
 
+ config.local.php
+ Pfade Include-Pfad/Smarty-Verzeichnisse
+ DSN  Includieren der Datei in Variable
+ Zeitzone
+ Error Reporting
+ config.php
+ service.php Enthält alle relevanten Hilfsroutinen (Framework)
+ Bibliotheken MDB2/Auth/Smarty
+ Klasseninterfaces
+ Konstanten
+ Steuerungsvariablen Links auf die einzelnen Bereiche

###Initialisierung
erfolgt in der "index.php"

+ Authentifizierung (Automatisch als User "Gast". Abmelden erzeugt ein neues Loginfenster. Im Moment noch etwas 
Baustelle.)
+ Bereitstellung einer persistenten Datenbankverbindung
+ GET-Behandlung (Login/Logout Prozedur und Spracheinstellung)
+ Setzen der locale in der DB
+ Laden der Klassenbibliotheken der Applikation
+ Initialisierung der Stringtabelle anhand der Spracheinstellung
+ Laden Menübereich (Zusammenstellung anhand der Rechte und Sprache des Users)
+ Schreiben des Headers

damit ist die Initialisierung abgeschlossen und der Mainframe wird geladen  
###Applikation
Als eine der Hauptursachen für fehlerhafte Initialisierung von Objekten, konnte die
Casting-Tabelle ausgemacht werden. Filme/Bücher und Namen/Personen sind nicht via Fremdschlüssel verbunden.
Wenn also nun ein Eintrag für eine Person mit einem Film vorliegt, der in der DB nicht existiert, dann wird
die Initialierungsprozedur über die Casting-Tabelle versuchen das Objekt Film zu initialisieren - was mit
einer Fehlermeldung endet.<br>Ein weiteres Problem in diesem Zusammenhang ist, das die Initialiserung sehr
viel Ressourcen verschlingt (vor allem mit anwachsender Casting-Liste). Ressourcen, die z.B. bei der
Listenansicht überhaupt nicht benötigt werden. Hier wäre über eine "schmalere" Initialisierungsprozedur
nachzudenken. Eventuell muss man ja nicht das Film-/Personenobjekt initialisieren, wenn man Einträge aus der
Castingliste benötigt!