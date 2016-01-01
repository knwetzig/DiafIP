DiafIP
======
___ist ein Frontend zur Eingabe in ein Datenbanksystem und Auswertung der Daten aus demselben.___

HINWEISE:
---------
Vorausgesetzt ist, das eine lauffähige HTTP/PHP5 Umgebung installiert ist. In diese
Umgebung ist das PHP-Modul 'php5-pgsql' zu integrieren. In den meisten Distrubutionen
ist nur die 'mysql' Unterstützung einkompiliert.

Installieren Sie nun, soweit nicht bereits geschehen die PEAR-Bibliotheken:  
    Auth,  
    MDB2 incl. des postgres-Treiber (Bestandteil des MDB2-Pakets)

Vor Implementierung ist die Datenbankstruktur aufzubauen. Dazu existiert das Verzeichnis
'data' mit seiner Grundstruktur 'all.sql' und nummerierten Versionsergänzungen.
Starten sie den Dump mit den Rechten des Datenbankbesitzers.

Anschließend ist der DSN außerhalb des Document-Root zu deklarieren.  
    Bsp.:      DSN = 'pgsql://dbuser:password@tcp(localhost)/diafip';

Im Verzeichnis 'configs' findet sich eine Vorlage für die zu erstellende Datei
'configs/config.local.php'. Passen Sie die Einstellungen an Ihre Bedürfnisse an.

RICHTLINIEN:
------------
+ Es gibt keine Verlinkung zu fremden Inhalten (Serverautonomie). Fremder Inhalt ist OpenSource und local abgelegt. 
Die einzelnen Elemente stehen in nachfolg. Tabelle:


    Name                        Zweck                            Lizenz
    ____________________________________________________________________  
       overLib                 JavaScript Popup-Bibliothek         GPL  
       PHP5                                                        PHP  
       Pear::Auth              Bibliothek Authentification         PHP  
       Pear::MDB2              Database Abstractionslayer          BSD-Style  
       PEAR::MDB2-Driver_pgsql MDB2 Driver                         BSD  
Detaillierte Angaben unter:
[opensource.org](https://opensource.org/licenses/ "Beschreibung der einzelnen Lizenzen")  


+ Es gibt verschiedene Nutzerprofile. Die einzelnen Profile stehen im Verzeichnis '/profile/'. Sie steuern das 
Erscheinungsbild der Applikation, nicht die Funktionalität. Diese wird durch die Templates für alle angemeldeten 
Nutzer bereit gestellt.  

FIXES:
------
Typenübergabe aus DB funktioniert nicht mit MDB2-pg_driver 1.5.0b4
Boolsche Werte werden als 'f' und 't' wiedergegeben statt als 'true' und 'null'.


