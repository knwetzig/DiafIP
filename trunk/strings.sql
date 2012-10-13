--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2012-10-13 10:01:01 CEST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 1888 (class 0 OID 16724)
-- Dependencies: 1576
-- Data for Name: s_strings; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO s_strings (id, de, en) VALUES (100, 'Bitte einen gültigen Titel eingeben.', 'Please enter a valid title');
INSERT INTO s_strings (id, de, en) VALUES (500, 'Originaltitel', 'Original title');
INSERT INTO s_strings (id, de, en) VALUES (501, 'Untertitel', 'Subtitle');
INSERT INTO s_strings (id, de, en) VALUES (503, 'Arbeitstitel', 'Working title');
INSERT INTO s_strings (id, de, en) VALUES (505, 'Folge', 'Episode');
INSERT INTO s_strings (id, de, en) VALUES (504, 'Serientitel', 'Series title');
INSERT INTO s_strings (id, de, en) VALUES (506, 'Beschreibung', 'Synopsis');
INSERT INTO s_strings (id, de, en) VALUES (4000, 'Titel', 'Titel');
INSERT INTO s_strings (id, de, en) VALUES (4001, 'Filme', 'Films');
INSERT INTO s_strings (id, de, en) VALUES (4002, 'Kopien', 'Copies');
INSERT INTO s_strings (id, de, en) VALUES (4003, 'Personen', 'Persons');
INSERT INTO s_strings (id, de, en) VALUES (4006, 'Einstellungen', 'Preferences');
INSERT INTO s_strings (id, de, en) VALUES (4008, 'Filmografie', 'Filmography');
INSERT INTO s_strings (id, de, en) VALUES (4009, 'DB-Statistik', 'DB-Statistics');
INSERT INTO s_strings (id, de, en) VALUES (4019, 'Daten sichern', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4017, 'neues Passwort', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4005, 'abmelden', 'check out');
INSERT INTO s_strings (id, de, en) VALUES (4004, 'anmelden', 'check in');
INSERT INTO s_strings (id, de, en) VALUES (4, 'Fehler aufgrund fehlender/falscher Parameter', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4010, 'Titelverzeichnis', 'Track Listings');
INSERT INTO s_strings (id, de, en) VALUES (4012, 'Personenverwaltung', 'People management');
INSERT INTO s_strings (id, de, en) VALUES (4007, 'Verwaltung', 'Administration');
INSERT INTO s_strings (id, de, en) VALUES (510, 'Anschrift', 'Adress');
INSERT INTO s_strings (id, de, en) VALUES (509, 'gest.', 'dead');
INSERT INTO s_strings (id, de, en) VALUES (502, 'geb.', 'born');
INSERT INTO s_strings (id, de, en) VALUES (511, 'tel', 'fon');
INSERT INTO s_strings (id, de, en) VALUES (512, 'eMail', 'email');
INSERT INTO s_strings (id, de, en) VALUES (513, 'Biografie', 'Biography');
INSERT INTO s_strings (id, de, en) VALUES (514, 'Notiz', 'note');
INSERT INTO s_strings (id, de, en) VALUES (515, 'Künstlername', 'Alias');
INSERT INTO s_strings (id, de, en) VALUES (517, 'Name', 'Name');
INSERT INTO s_strings (id, de, en) VALUES (516, 'Vorname', 'first name');
INSERT INTO s_strings (id, de, en) VALUES (4014, 'in', 'into');
INSERT INTO s_strings (id, de, en) VALUES (110, 'Bitte geben sie 2 gleiche Passwörter ein', NULL);
INSERT INTO s_strings (id, de, en) VALUES (3, 'Die Operation war erfolgreich', NULL);
INSERT INTO s_strings (id, de, en) VALUES (1, 'Die Daten konnten auf Grund eines internen Fehlers nicht gespeichert werden.', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10001, 'Neuen Personendatensatz anlegen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (101, 'Die Suchanfrage enthielt Sonderzeichen oder war leer', NULL);
INSERT INTO s_strings (id, de, en) VALUES (102, 'Die Suchanfrage brachte keine Ergebnisse', NULL);
INSERT INTO s_strings (id, de, en) VALUES (103, 'Die Datumsangabe war fehlerhaft', NULL);
INSERT INTO s_strings (id, de, en) VALUES (104, 'Die Postleitzahl kann nicht stimmen!', NULL);
INSERT INTO s_strings (id, de, en) VALUES (105, 'Die Telefonnummer ist ungültig', NULL);
INSERT INTO s_strings (id, de, en) VALUES (106, 'Die Email-adresse hat eine fehlerhafte Syntax', NULL);
INSERT INTO s_strings (id, de, en) VALUES (107, 'Geben Sie einen Namen ein', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10002, 'Beispiel: +49 351 9876543<br />Erlaubt sind Plus, Ziffern und Leerzeichen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (108, 'Melden Sie sich am System an, um die Seite zu nutzen.', NULL);
INSERT INTO s_strings (id, de, en) VALUES (109, 'Bitte benutzen sie nur Alphanumerische Zeichen (Ziffern und Buchstaben).', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4015, 'Passwort ändern', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4018, 'Passwortwiederholung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4016, 'Accountname', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10000, 'Beispiele: 24.3.2002 | 24.3.02 | 20020324 | 2002-3-24<br />Falls der Monat/Tag nicht bekannt ist, jeweils durch 1 ersetzen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (0, '-- kein Eintrag --', '-- no message --');
INSERT INTO s_strings (id, de, en) VALUES (8, 'Fehler bei der Verarbeitung von Daten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4011, 'Suchmaske', 'user search');
INSERT INTO s_strings (id, de, en) VALUES (520, 'Regie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (521, 'Künstlerische Leitung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (522, 'Kamera', NULL);
INSERT INTO s_strings (id, de, en) VALUES (523, 'Kamera Assistenz', NULL);
INSERT INTO s_strings (id, de, en) VALUES (524, 'Trick', NULL);
INSERT INTO s_strings (id, de, en) VALUES (525, 'Spezial Effekte', NULL);
INSERT INTO s_strings (id, de, en) VALUES (526, 'Beleuchtung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (527, 'Fotograf', NULL);
INSERT INTO s_strings (id, de, en) VALUES (528, 'Compositing', NULL);
INSERT INTO s_strings (id, de, en) VALUES (529, 'Drehbuch', NULL);
INSERT INTO s_strings (id, de, en) VALUES (530, 'Dramaturgie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (531, 'Szenarium', NULL);
INSERT INTO s_strings (id, de, en) VALUES (532, 'Dialoge', NULL);
INSERT INTO s_strings (id, de, en) VALUES (533, 'Beratung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (534, 'Sprecher', NULL);
INSERT INTO s_strings (id, de, en) VALUES (535, 'Darsteller', NULL);
INSERT INTO s_strings (id, de, en) VALUES (536, 'Schnitt', NULL);
INSERT INTO s_strings (id, de, en) VALUES (537, 'Ton', NULL);
INSERT INTO s_strings (id, de, en) VALUES (538, 'Geräusche', NULL);
INSERT INTO s_strings (id, de, en) VALUES (539, 'Komposition', NULL);
INSERT INTO s_strings (id, de, en) VALUES (540, 'Lieder', NULL);
INSERT INTO s_strings (id, de, en) VALUES (541, 'Bearbeitung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (542, 'Animation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (543, 'Ausstattung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (544, 'Entwurf', NULL);
INSERT INTO s_strings (id, de, en) VALUES (545, 'Figuren', NULL);
INSERT INTO s_strings (id, de, en) VALUES (546, 'Szenografie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (547, 'Psaligrafie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (548, 'Bauten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (549, 'Titelgrafik', NULL);
INSERT INTO s_strings (id, de, en) VALUES (550, 'Synchronisation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (551, 'Produzent', NULL);
INSERT INTO s_strings (id, de, en) VALUES (552, 'Aufnameleitung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (553, 'Redaktion', NULL);
INSERT INTO s_strings (id, de, en) VALUES (554, 'Koproduzent', NULL);
INSERT INTO s_strings (id, de, en) VALUES (555, 'Auftraggeber', NULL);
INSERT INTO s_strings (id, de, en) VALUES (556, 'Koregie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (558, 'Hersteller', NULL);
INSERT INTO s_strings (id, de, en) VALUES (600, 'Zeichentrick', NULL);
INSERT INTO s_strings (id, de, en) VALUES (601, 'Puppentrick', NULL);
INSERT INTO s_strings (id, de, en) VALUES (602, 'Materialanimation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (559, 'Lizenzgeber', NULL);
INSERT INTO s_strings (id, de, en) VALUES (603, 'Handpuppen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (604, 'Silhouetten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (561, 'Propaganda', NULL);
INSERT INTO s_strings (id, de, en) VALUES (560, 'Werbung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (562, 'Musik', NULL);
INSERT INTO s_strings (id, de, en) VALUES (563, 'Videoclip', NULL);
INSERT INTO s_strings (id, de, en) VALUES (564, 'Experimental', NULL);
INSERT INTO s_strings (id, de, en) VALUES (605, 'Flachfiguren', NULL);
INSERT INTO s_strings (id, de, en) VALUES (565, 'Installation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (566, 'Dokumentation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (567, 'TV-Film', NULL);
INSERT INTO s_strings (id, de, en) VALUES (568, 'TV-Serie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (569, 'Instruktion', NULL);
INSERT INTO s_strings (id, de, en) VALUES (570, 'Lehre', NULL);
INSERT INTO s_strings (id, de, en) VALUES (606, 'Computeranimation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4021, 'Titel löschen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (607, 'Realfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4022, 'Titel bearbeiten', 'edit titel');
INSERT INTO s_strings (id, de, en) VALUES (4023, 'Neuanlage Titel', 'new plant titel');
INSERT INTO s_strings (id, de, en) VALUES (4025, 'Bearbeiten von Titeln', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4024, 'Neuanlage filmografischer Datensatz', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4026, 'Neuanlage bibliografischer Datensatz', NULL);
INSERT INTO s_strings (id, de, en) VALUES (2, 'Sie haben keine ausreichende Berechtigung hierfür', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10004, '<b>ACHTUNG: </b>Sie haben dem Nutzer alle Rechte entzogen!', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10005, '<b>ACHTUNG: </b>Das setzen von Sterbedaten führt zur Löschung von Anschriften, Telefonnummern und Mailadressen, weil Tote eben keinen Wohnsitz haben.', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10006, 'Der Datensatz konnte nicht gelöscht werden, weil die Daten<br />mit anderen Einträgen verknüpft sind.<br />Löschen sie erst diese und versuchen Sie es dann erneut.', NULL);
INSERT INTO s_strings (id, de, en) VALUES (571, 'Produktionstechnik', NULL);
INSERT INTO s_strings (id, de, en) VALUES (572, 'Anmerkungen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (573, 's/w-Film', NULL);
INSERT INTO s_strings (id, de, en) VALUES (574, 'Stummfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (576, 'Produktionsjahr', NULL);
INSERT INTO s_strings (id, de, en) VALUES (64, 'reserviert fehler 6', NULL);
INSERT INTO s_strings (id, de, en) VALUES (32, 'reserviert fehler 5', NULL);
INSERT INTO s_strings (id, de, en) VALUES (16, 'reserviert fehler 4', NULL);
INSERT INTO s_strings (id, de, en) VALUES (256, 'reserviert fehler 8', NULL);
INSERT INTO s_strings (id, de, en) VALUES (577, 'Thema', NULL);
INSERT INTO s_strings (id, de, en) VALUES (578, 'Quellen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (579, 'Gattung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (580, 'Laufzeit', NULL);
INSERT INTO s_strings (id, de, en) VALUES (581, 'Altersempfehlung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (582, 'Prädikat', NULL);
INSERT INTO s_strings (id, de, en) VALUES (583, 'Mediaspezifikation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (584, 'Erstaufführung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4013, 'Datensatz bearbeiten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4020, 'Datensatz löschen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (585, 'vorzeigbar', NULL);
INSERT INTO s_strings (id, de, en) VALUES (557, 'Besonders wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (575, 'ohne&nbsp;Sprache', NULL);
INSERT INTO s_strings (id, de, en) VALUES (128, 'Datensatz bereits vorhanden', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10007, 'Werte vom Typ interval können mit der folgenden Syntax geschrieben werden:<br />zahl einheit [zahl einheit...]<br />Wobei: zahl eine Zahl ist, einheit eines der Folgenden ist: second (Sekunde), minute (Minute), hour (Stunde), day (Tag), week (Woche), month (Monat), year (Jahr), decade (Jahrzehnt), century (Jahrhundert), millennium (Jahrtausend), oder eine Abkürzung oder die Mehrzahl von einer dieser Einheiten. Die Werte in den unterschiedlichen Einheiten werden automatisch unter Beachtung der Vorzeichen zusammengezählt. Werte für Tage, Stunden, Minuten und Sekunden können ohne ausdrückliche Einheiten angegeben werden. Zum Beispiel: <pre>1 12:59:10</pre> entspricht <pre>1 day 12 hours 59 min 10 sec</pre>. ', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10008, '<strong>Warnung: </strong>Es existieren bereits Einträge zu diesem Titel!', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10009, 'alle Daten vollständig und überprüft', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4027, 'Stammdaten', NULL);


-- Completed on 2012-10-13 10:01:02 CEST

--
-- PostgreSQL database dump complete
--

