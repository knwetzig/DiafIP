--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2012-08-30 14:16:53 CEST
-- $Id:$

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 1878 (class 0 OID 16724)
-- Dependencies: 1569
-- Data for Name: s_strings; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO s_strings VALUES (100, 'Bitte einen gültigen Titel eingeben.', 'Please enter a valid title');
INSERT INTO s_strings VALUES (500, 'Originaltitel', 'Original title');
INSERT INTO s_strings VALUES (501, 'Untertitel', 'Subtitle');
INSERT INTO s_strings VALUES (503, 'Arbeitstitel', 'Working title');
INSERT INTO s_strings VALUES (505, 'Folge', 'Episode');
INSERT INTO s_strings VALUES (504, 'Serientitel', 'Series title');
INSERT INTO s_strings VALUES (506, 'Beschreibung', 'Synopsis');
INSERT INTO s_strings VALUES (4000, 'Titel', 'Titel');
INSERT INTO s_strings VALUES (4001, 'Filme', 'Films');
INSERT INTO s_strings VALUES (4002, 'Kopien', 'Copies');
INSERT INTO s_strings VALUES (4003, 'Personen', 'Persons');
INSERT INTO s_strings VALUES (4006, 'Einstellungen', 'Preferences');
INSERT INTO s_strings VALUES (4008, 'Filmografie', 'Filmography');
INSERT INTO s_strings VALUES (4009, 'DB-Statistik', 'DB-Statistics');
INSERT INTO s_strings VALUES (4019, 'Daten sichern', NULL);
INSERT INTO s_strings VALUES (4017, 'neues Passwort', NULL);
INSERT INTO s_strings VALUES (4005, 'abmelden', 'check out');
INSERT INTO s_strings VALUES (4004, 'anmelden', 'check in');
INSERT INTO s_strings VALUES (4010, 'Titelverzeichnis', 'Track Listings');
INSERT INTO s_strings VALUES (4012, 'Personenverwaltung', 'People management');
INSERT INTO s_strings VALUES (4007, 'Verwaltung', 'Administration');
INSERT INTO s_strings VALUES (510, 'Anschrift', 'Adress');
INSERT INTO s_strings VALUES (509, 'gest.', 'dead');
INSERT INTO s_strings VALUES (502, 'geb.', 'born');
INSERT INTO s_strings VALUES (511, 'tel', 'fon');
INSERT INTO s_strings VALUES (512, 'eMail', 'email');
INSERT INTO s_strings VALUES (513, 'Biografie', 'Biography');
INSERT INTO s_strings VALUES (514, 'Notiz', 'note');
INSERT INTO s_strings VALUES (515, 'Künstlername', 'Alias');
INSERT INTO s_strings VALUES (517, 'Name', 'Name');
INSERT INTO s_strings VALUES (516, 'Vorname', 'first name');
INSERT INTO s_strings VALUES (4014, 'in', 'into');
INSERT INTO s_strings VALUES (110, 'Bitte geben sie 2 gleiche Passwörter ein', NULL);
INSERT INTO s_strings VALUES (3, 'Die Operation war erfolgreich', NULL);
INSERT INTO s_strings VALUES (10000, 'Falls der Monat/Tag nicht bekannt ist, jeweils durch 1 ersetzen', NULL);
INSERT INTO s_strings VALUES (1, 'Die Daten konnten auf Grund eines internen Fehlers nicht gespeichert werden.', NULL);
INSERT INTO s_strings VALUES (10001, 'Neuen Personendatensatz anlegen', NULL);
INSERT INTO s_strings VALUES (4013, 'Personen bearbeiten', NULL);
INSERT INTO s_strings VALUES (101, 'Die Suchanfrage enthielt Sonderzeichen oder war leer', NULL);
INSERT INTO s_strings VALUES (102, 'Die Suchanfrage brachte keine Ergebnisse', NULL);
INSERT INTO s_strings VALUES (103, 'Die Datumsangabe war fehlerhaft', NULL);
INSERT INTO s_strings VALUES (104, 'Die Postleitzahl kann nicht stimmen!', NULL);
INSERT INTO s_strings VALUES (105, 'Die Telefonnummer ist ungültig', NULL);
INSERT INTO s_strings VALUES (106, 'Die Email-adresse hat eine fehlerhafte Syntax', NULL);
INSERT INTO s_strings VALUES (107, 'Geben Sie einen Namen ein', NULL);
INSERT INTO s_strings VALUES (10002, 'Beispiel: +49 351 9876543<br />Erlaubt sind Plus, Ziffern und Leerzeichen', NULL);
INSERT INTO s_strings VALUES (108, 'Melden Sie sich am System an, um die Seite zu nutzen.', NULL);
INSERT INTO s_strings VALUES (109, 'Bitte benutzen sie nur Alphanumerische Zeichen (Ziffern und Buchstaben).', NULL);
INSERT INTO s_strings VALUES (4015, 'Passwort ändern', NULL);
INSERT INTO s_strings VALUES (4018, 'Passwortwiederholung', NULL);
INSERT INTO s_strings VALUES (4016, 'Accountname', NULL);
INSERT INTO s_strings VALUES (4020, 'Person löschen', NULL);
INSERT INTO s_strings VALUES (0, '-- kein Eintrag --', '-- no message --');
INSERT INTO s_strings VALUES (4011, 'Suchmaske', 'user search');
INSERT INTO s_strings VALUES (520, 'Regie', NULL);
INSERT INTO s_strings VALUES (521, 'Künstlerische Leitung', NULL);
INSERT INTO s_strings VALUES (522, 'Kamera', NULL);
INSERT INTO s_strings VALUES (523, 'Kamera Assistenz', NULL);
INSERT INTO s_strings VALUES (524, 'Trick', NULL);
INSERT INTO s_strings VALUES (525, 'Spezial Effekte', NULL);
INSERT INTO s_strings VALUES (526, 'Beleuchtung', NULL);
INSERT INTO s_strings VALUES (527, 'Fotograf', NULL);
INSERT INTO s_strings VALUES (528, 'Compositing', NULL);
INSERT INTO s_strings VALUES (529, 'Drehbuch', NULL);
INSERT INTO s_strings VALUES (530, 'Dramaturgie', NULL);
INSERT INTO s_strings VALUES (531, 'Szenarium', NULL);
INSERT INTO s_strings VALUES (532, 'Dialoge', NULL);
INSERT INTO s_strings VALUES (533, 'Beratung', NULL);
INSERT INTO s_strings VALUES (534, 'Sprecher', NULL);
INSERT INTO s_strings VALUES (535, 'Darsteller', NULL);
INSERT INTO s_strings VALUES (536, 'Schnitt', NULL);
INSERT INTO s_strings VALUES (537, 'Ton', NULL);
INSERT INTO s_strings VALUES (538, 'Geräusche', NULL);
INSERT INTO s_strings VALUES (539, 'Komposition', NULL);
INSERT INTO s_strings VALUES (540, 'Lieder', NULL);
INSERT INTO s_strings VALUES (541, 'Bearbeitung', NULL);
INSERT INTO s_strings VALUES (542, 'Animation', NULL);
INSERT INTO s_strings VALUES (543, 'Ausstattung', NULL);
INSERT INTO s_strings VALUES (544, 'Entwurf', NULL);
INSERT INTO s_strings VALUES (545, 'Figuren', NULL);
INSERT INTO s_strings VALUES (546, 'Szenografie', NULL);
INSERT INTO s_strings VALUES (547, 'Psaligrafie', NULL);
INSERT INTO s_strings VALUES (548, 'Bauten', NULL);
INSERT INTO s_strings VALUES (549, 'Titelgrafik', NULL);
INSERT INTO s_strings VALUES (550, 'Synchronisation', NULL);
INSERT INTO s_strings VALUES (551, 'Produzent', NULL);
INSERT INTO s_strings VALUES (552, 'Aufnameleitung', NULL);
INSERT INTO s_strings VALUES (553, 'Redaktion', NULL);
INSERT INTO s_strings VALUES (554, 'Koproduzent', NULL);
INSERT INTO s_strings VALUES (555, 'Auftraggeber', NULL);
INSERT INTO s_strings VALUES (556, 'Koregie', NULL);
INSERT INTO s_strings VALUES (557, 'Besonders Wertvoll', NULL);
INSERT INTO s_strings VALUES (558, 'Hersteller', NULL);
INSERT INTO s_strings VALUES (559, 'Lizenzgeber', NULL);
INSERT INTO s_strings VALUES (561, 'Propaganda', NULL);
INSERT INTO s_strings VALUES (560, 'Werbung', NULL);
INSERT INTO s_strings VALUES (562, 'Musik', NULL);
INSERT INTO s_strings VALUES (563, 'Videoclip', NULL);
INSERT INTO s_strings VALUES (564, 'Experimental', NULL);
INSERT INTO s_strings VALUES (565, 'Installation', NULL);
INSERT INTO s_strings VALUES (566, 'Dokumentation', NULL);
INSERT INTO s_strings VALUES (567, 'TV-Film', NULL);
INSERT INTO s_strings VALUES (568, 'TV-Serie', NULL);
INSERT INTO s_strings VALUES (569, 'Instruktion', NULL);
INSERT INTO s_strings VALUES (570, 'Lehre', NULL);
INSERT INTO s_strings VALUES (4021, 'Titel löschen', NULL);
INSERT INTO s_strings VALUES (4022, 'Titel bearbeiten', 'edit titel');
INSERT INTO s_strings VALUES (4023, 'Neuanlage Titel', 'new plant titel');
INSERT INTO s_strings VALUES (4025, 'Bearbeiten von Titeln', NULL);
INSERT INTO s_strings VALUES (4024, 'Neuanlage filmografischer Datensatz', NULL);
INSERT INTO s_strings VALUES (4026, 'Neuanlage bibliografischer Datensatz', NULL);
INSERT INTO s_strings VALUES (2, 'Sie haben keine ausreichende Berechtigung hierfür', NULL);
INSERT INTO s_strings VALUES (10004, '<b>ACHTUNG: </b>Sie haben dem Nutzer alle Rechte entzogen!', NULL);
INSERT INTO s_strings VALUES (10005, '<b>ACHTUNG: </b>Das setzen von Sterbedaten führt zur Löschung von Anschriften, Telefonnummern und Mailadressen, weil Tote eben keinen Wohnsitz haben.', NULL);
INSERT INTO s_strings VALUES (10006, 'Der Datensatz konnte nicht gelöscht werden, weil die Daten<br />mit anderen Einträgen verknüpft sind.<br />Löschen sie erst diese und versuchen Sie es dann erneut.', NULL);
INSERT INTO s_strings VALUES (4, 'Fehler aufgrund fehlender Parameter', NULL);
INSERT INTO s_strings VALUES (64, 'reserviert fehler 6', NULL);
INSERT INTO s_strings VALUES (32, 'reserviert fehler 5', NULL);
INSERT INTO s_strings VALUES (16, 'reserviert fehler 4', NULL);
INSERT INTO s_strings VALUES (8, 'reserviert fehler 3', NULL);
INSERT INTO s_strings VALUES (128, 'reserviert fehler 7', NULL);
INSERT INTO s_strings VALUES (256, 'reserviert fehler 8', NULL);


-- Completed on 2012-08-30 14:16:54 CEST

--
-- PostgreSQL database dump complete
--

