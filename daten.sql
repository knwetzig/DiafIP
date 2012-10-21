--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2012-10-21 21:35:40 CEST

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

--
-- TOC entry 1926 (class 0 OID 0)
-- Dependencies: 1609
-- Name: f_bformat_id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('f_bformat_id_seq', 6, true);


--
-- TOC entry 1927 (class 0 OID 0)
-- Dependencies: 1590
-- Name: s_land_id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('s_land_id_seq', 1, false);


--
-- TOC entry 1923 (class 0 OID 17739)
-- Dependencies: 1608
-- Data for Name: f_bformat; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_bformat (format, id) VALUES ('4:3', 1);
INSERT INTO f_bformat (format, id) VALUES ('1:1,85', 3);
INSERT INTO f_bformat (format, id) VALUES ('Cinemascope', 4);
INSERT INTO f_bformat (format, id) VALUES ('1:1,66 (16:10)', 2);
INSERT INTO f_bformat (format, id) VALUES ('1:1,77 (16:9)', 5);


--
-- TOC entry 1916 (class 0 OID 16724)
-- Dependencies: 1580
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
INSERT INTO s_strings (id, de, en) VALUES (605, 'Flachfiguren', NULL);
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
INSERT INTO s_strings (id, de, en) VALUES (580, 'Laufzeit', NULL);
INSERT INTO s_strings (id, de, en) VALUES (581, 'Altersempfehlung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (582, 'Prädikat', NULL);
INSERT INTO s_strings (id, de, en) VALUES (583, 'Mediaspezifikation', NULL);
INSERT INTO s_strings (id, de, en) VALUES (584, 'Erstaufführung', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4013, 'Datensatz bearbeiten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4020, 'Datensatz löschen', NULL);
INSERT INTO s_strings (id, de, en) VALUES (575, 'ohne&nbsp;Sprache', NULL);
INSERT INTO s_strings (id, de, en) VALUES (128, 'Datensatz bereits vorhanden', NULL);
INSERT INTO s_strings (id, de, en) VALUES (557, '-- frei --', NULL);
INSERT INTO s_strings (id, de, en) VALUES (658, 'künstlerisch', NULL);
INSERT INTO s_strings (id, de, en) VALUES (579, 'Genre', NULL);
INSERT INTO s_strings (id, de, en) VALUES (565, 'Autorenfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (566, 'Dokumentarfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (567, 'TV-Film / -serie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (568, 'Komödie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10007, 'Werte vom Typ interval können mit der folgenden Syntax geschrieben werden:<br />zahl einheit [zahl einheit...]<br />Wobei: zahl eine Zahl ist, einheit eines der Folgenden ist: second (Sekunde), minute (Minute), hour (Stunde), day (Tag), week (Woche), month (Monat), year (Jahr), decade (Jahrzehnt), century (Jahrhundert), millennium (Jahrtausend), oder eine Abkürzung oder die Mehrzahl von einer dieser Einheiten. Die Werte in den unterschiedlichen Einheiten werden automatisch unter Beachtung der Vorzeichen zusammengezählt. Werte für Tage, Stunden, Minuten und Sekunden können ohne ausdrückliche Einheiten angegeben werden. Zum Beispiel: <pre>1 12:59:10</pre> entspricht <pre>1 day 12 hours 59 min 10 sec</pre>. ', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10008, '<strong>Warnung: </strong>Es existieren bereits Einträge zu diesem Titel!', NULL);
INSERT INTO s_strings (id, de, en) VALUES (10009, 'alle Daten vollständig und überprüft', NULL);
INSERT INTO s_strings (id, de, en) VALUES (4027, 'Stammdaten', NULL);
INSERT INTO s_strings (id, de, en) VALUES (608, 'Bildformat', NULL);
INSERT INTO s_strings (id, de, en) VALUES (453, 'Upload-Verzeichnis nicht gefunden', 'Upload folder not found');
INSERT INTO s_strings (id, de, en) VALUES (454, 'Kann hochgeladene Datei nicht speichern', 'Unable to write uploaded file');
INSERT INTO s_strings (id, de, en) VALUES (456, 'Unbekannter Fehler', 'Unknown error');
INSERT INTO s_strings (id, de, en) VALUES (457, 'Bild nicht in hochgeladen Daten gefunden', 'Image not found in uploaded data');
INSERT INTO s_strings (id, de, en) VALUES (458, 'Datei ist keine hochgeladene Datei', 'File is not an uploaded file');
INSERT INTO s_strings (id, de, en) VALUES (459, 'Datei ist keine Bilddatei', 'File is not an image');
INSERT INTO s_strings (id, de, en) VALUES (450, 'Die hochgeladene Datei überschreitet die festgelegte Größe. ', 'Image is too large');
INSERT INTO s_strings (id, de, en) VALUES (451, 'Die Datei wurde nur teilweise hochgeladen.', 'Image was only partially uploaded');
INSERT INTO s_strings (id, de, en) VALUES (452, 'Keine Datei hochgeladen', 'No image was uploaded');
INSERT INTO s_strings (id, de, en) VALUES (455, 'Systemkomponente hat das hochladen gestoppt', 'Upload failed due to extension');
INSERT INTO s_strings (id, de, en) VALUES (659, 'volksbildend', NULL);
INSERT INTO s_strings (id, de, en) VALUES (660, 'Lehrfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (661, 'künstlerisch besonders wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (662, 'staatspolitisch wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (663, 'kulturell wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (664, 'staatspolitisch und künstlerisch besonders wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (665, 'staatsplitisch wertvoll, küntlerisch wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (666, 'staatspolitisch besonders wertvoll, künstlerisch besonders wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (667, 'volkstümlich wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (668, 'jugendwert', NULL);
INSERT INTO s_strings (id, de, en) VALUES (669, 'anerkennenswert', NULL);
INSERT INTO s_strings (id, de, en) VALUES (650, 'Besonders wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (651, 'Wertvoll', NULL);
INSERT INTO s_strings (id, de, en) VALUES (670, 'Sehenswert', NULL);
INSERT INTO s_strings (id, de, en) VALUES (672, 'Tragikomödie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (673, 'Tragödie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (674, 'Actionfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (675, 'Film noir', NULL);
INSERT INTO s_strings (id, de, en) VALUES (676, 'Liebesfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (677, 'Thriller', NULL);
INSERT INTO s_strings (id, de, en) VALUES (678, 'Horrorfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (679, 'Erotikfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (680, 'Abenteuerfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (681, 'Katastrophenfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (682, 'Kriminalfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (683, 'Fantasyfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (684, 'Mysteryfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (685, 'Mileustudie', NULL);
INSERT INTO s_strings (id, de, en) VALUES (687, 'Heimatfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (688, 'Western', NULL);
INSERT INTO s_strings (id, de, en) VALUES (689, 'Historienfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (690, 'Kriegsfilm /Antikriegsfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (691, 'Martial-Arts-Film / Eastern', NULL);
INSERT INTO s_strings (id, de, en) VALUES (686, 'Science-Fiction-Film / Utopischer Film', NULL);
INSERT INTO s_strings (id, de, en) VALUES (564, 'Independentfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (692, 'Kinderfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (693, 'Jugendfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (694, 'Frauenfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (695, 'Familienfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (696, 'Märchenfilm', NULL);
INSERT INTO s_strings (id, de, en) VALUES (697, 'Expressionistischer Film', NULL);
INSERT INTO s_strings (id, de, en) VALUES (698, 'Nouvelle Vague', NULL);
INSERT INTO s_strings (id, de, en) VALUES (699, 'Neorealismus', NULL);
INSERT INTO s_strings (id, de, en) VALUES (700, 'Poetischer Realismus', NULL);
INSERT INTO s_strings (id, de, en) VALUES (671, 'Melodram', NULL);


--
-- TOC entry 1919 (class 0 OID 17390)
-- Dependencies: 1599 1916
-- Data for Name: f_genre; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_genre (gattung) VALUES (560);
INSERT INTO f_genre (gattung) VALUES (561);
INSERT INTO f_genre (gattung) VALUES (562);
INSERT INTO f_genre (gattung) VALUES (563);
INSERT INTO f_genre (gattung) VALUES (564);
INSERT INTO f_genre (gattung) VALUES (565);
INSERT INTO f_genre (gattung) VALUES (566);
INSERT INTO f_genre (gattung) VALUES (567);
INSERT INTO f_genre (gattung) VALUES (568);
INSERT INTO f_genre (gattung) VALUES (569);
INSERT INTO f_genre (gattung) VALUES (570);
INSERT INTO f_genre (gattung) VALUES (671);
INSERT INTO f_genre (gattung) VALUES (672);
INSERT INTO f_genre (gattung) VALUES (673);
INSERT INTO f_genre (gattung) VALUES (674);
INSERT INTO f_genre (gattung) VALUES (675);
INSERT INTO f_genre (gattung) VALUES (676);
INSERT INTO f_genre (gattung) VALUES (677);
INSERT INTO f_genre (gattung) VALUES (678);
INSERT INTO f_genre (gattung) VALUES (679);
INSERT INTO f_genre (gattung) VALUES (680);
INSERT INTO f_genre (gattung) VALUES (681);
INSERT INTO f_genre (gattung) VALUES (682);
INSERT INTO f_genre (gattung) VALUES (683);
INSERT INTO f_genre (gattung) VALUES (684);
INSERT INTO f_genre (gattung) VALUES (685);
INSERT INTO f_genre (gattung) VALUES (686);
INSERT INTO f_genre (gattung) VALUES (687);
INSERT INTO f_genre (gattung) VALUES (688);
INSERT INTO f_genre (gattung) VALUES (689);
INSERT INTO f_genre (gattung) VALUES (690);
INSERT INTO f_genre (gattung) VALUES (691);
INSERT INTO f_genre (gattung) VALUES (692);
INSERT INTO f_genre (gattung) VALUES (693);
INSERT INTO f_genre (gattung) VALUES (694);
INSERT INTO f_genre (gattung) VALUES (695);
INSERT INTO f_genre (gattung) VALUES (696);
INSERT INTO f_genre (gattung) VALUES (697);
INSERT INTO f_genre (gattung) VALUES (698);
INSERT INTO f_genre (gattung) VALUES (699);
INSERT INTO f_genre (gattung) VALUES (700);


--
-- TOC entry 1922 (class 0 OID 17728)
-- Dependencies: 1607
-- Data for Name: f_mediaspezi; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_mediaspezi (mediaspezi) VALUES (573);
INSERT INTO f_mediaspezi (mediaspezi) VALUES (574);
INSERT INTO f_mediaspezi (mediaspezi) VALUES (575);


--
-- TOC entry 1920 (class 0 OID 17405)
-- Dependencies: 1600 1916
-- Data for Name: f_praed; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_praed (praed) VALUES (0);
INSERT INTO f_praed (praed) VALUES (650);
INSERT INTO f_praed (praed) VALUES (651);
INSERT INTO f_praed (praed) VALUES (658);
INSERT INTO f_praed (praed) VALUES (659);
INSERT INTO f_praed (praed) VALUES (660);
INSERT INTO f_praed (praed) VALUES (661);
INSERT INTO f_praed (praed) VALUES (662);
INSERT INTO f_praed (praed) VALUES (663);
INSERT INTO f_praed (praed) VALUES (664);
INSERT INTO f_praed (praed) VALUES (665);
INSERT INTO f_praed (praed) VALUES (666);
INSERT INTO f_praed (praed) VALUES (667);
INSERT INTO f_praed (praed) VALUES (668);
INSERT INTO f_praed (praed) VALUES (669);
INSERT INTO f_praed (praed) VALUES (670);


--
-- TOC entry 1921 (class 0 OID 17708)
-- Dependencies: 1606 1916
-- Data for Name: f_prodtechnik; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_prodtechnik (beschreibung) VALUES (600);
INSERT INTO f_prodtechnik (beschreibung) VALUES (601);
INSERT INTO f_prodtechnik (beschreibung) VALUES (602);
INSERT INTO f_prodtechnik (beschreibung) VALUES (603);
INSERT INTO f_prodtechnik (beschreibung) VALUES (604);
INSERT INTO f_prodtechnik (beschreibung) VALUES (605);
INSERT INTO f_prodtechnik (beschreibung) VALUES (606);
INSERT INTO f_prodtechnik (beschreibung) VALUES (607);


--
-- TOC entry 1918 (class 0 OID 17299)
-- Dependencies: 1595 1916
-- Data for Name: f_taetig; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO f_taetig (taetig) VALUES (520);
INSERT INTO f_taetig (taetig) VALUES (521);
INSERT INTO f_taetig (taetig) VALUES (522);
INSERT INTO f_taetig (taetig) VALUES (523);
INSERT INTO f_taetig (taetig) VALUES (524);
INSERT INTO f_taetig (taetig) VALUES (525);
INSERT INTO f_taetig (taetig) VALUES (526);
INSERT INTO f_taetig (taetig) VALUES (527);
INSERT INTO f_taetig (taetig) VALUES (528);
INSERT INTO f_taetig (taetig) VALUES (529);
INSERT INTO f_taetig (taetig) VALUES (530);
INSERT INTO f_taetig (taetig) VALUES (531);
INSERT INTO f_taetig (taetig) VALUES (532);
INSERT INTO f_taetig (taetig) VALUES (533);
INSERT INTO f_taetig (taetig) VALUES (534);
INSERT INTO f_taetig (taetig) VALUES (535);
INSERT INTO f_taetig (taetig) VALUES (536);
INSERT INTO f_taetig (taetig) VALUES (537);
INSERT INTO f_taetig (taetig) VALUES (538);
INSERT INTO f_taetig (taetig) VALUES (539);
INSERT INTO f_taetig (taetig) VALUES (540);
INSERT INTO f_taetig (taetig) VALUES (541);
INSERT INTO f_taetig (taetig) VALUES (542);
INSERT INTO f_taetig (taetig) VALUES (543);
INSERT INTO f_taetig (taetig) VALUES (544);
INSERT INTO f_taetig (taetig) VALUES (545);
INSERT INTO f_taetig (taetig) VALUES (546);
INSERT INTO f_taetig (taetig) VALUES (547);
INSERT INTO f_taetig (taetig) VALUES (548);
INSERT INTO f_taetig (taetig) VALUES (549);
INSERT INTO f_taetig (taetig) VALUES (550);
INSERT INTO f_taetig (taetig) VALUES (551);
INSERT INTO f_taetig (taetig) VALUES (552);
INSERT INTO f_taetig (taetig) VALUES (553);
INSERT INTO f_taetig (taetig) VALUES (554);
INSERT INTO f_taetig (taetig) VALUES (555);
INSERT INTO f_taetig (taetig) VALUES (556);
INSERT INTO f_taetig (taetig) VALUES (558);
INSERT INTO f_taetig (taetig) VALUES (559);


--
-- TOC entry 1917 (class 0 OID 17156)
-- Dependencies: 1591
-- Data for Name: s_land; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

INSERT INTO s_land (id, land, bland) VALUES (1, 'Deutschland', 'Sachsen');
INSERT INTO s_land (id, land, bland) VALUES (2, 'Deutschland', 'Baden-Württemberg');
INSERT INTO s_land (id, land, bland) VALUES (3, 'Deutschland', 'Bayern');
INSERT INTO s_land (id, land, bland) VALUES (4, 'Deutschland', 'Berlin');
INSERT INTO s_land (id, land, bland) VALUES (5, 'Deutschland', 'Brandenburg');
INSERT INTO s_land (id, land, bland) VALUES (6, 'Deutschland', 'Bremen');
INSERT INTO s_land (id, land, bland) VALUES (7, 'Deutschland', 'Hamburg');
INSERT INTO s_land (id, land, bland) VALUES (8, 'Deutschland', 'Hessen');
INSERT INTO s_land (id, land, bland) VALUES (9, 'Deutschland', 'Mecklenburg-Vorpommern');
INSERT INTO s_land (id, land, bland) VALUES (10, 'Deutschland', 'Niedersachsen');
INSERT INTO s_land (id, land, bland) VALUES (11, 'Deutschland', 'Nordrhein-Westfalen');
INSERT INTO s_land (id, land, bland) VALUES (12, 'Deutschland', 'Rheinland-Pfalz');
INSERT INTO s_land (id, land, bland) VALUES (13, 'Deutschland', 'Saarland');
INSERT INTO s_land (id, land, bland) VALUES (14, 'Deutschland', 'Sachsen-Anhalt');
INSERT INTO s_land (id, land, bland) VALUES (15, 'Deutschland', 'Schleswig-Holstein');
INSERT INTO s_land (id, land, bland) VALUES (16, 'Deutschland', 'Thüringen');
INSERT INTO s_land (id, land, bland) VALUES (17, 'USA', 'Californien');


-- Completed on 2012-10-21 21:35:41 CEST

--
-- PostgreSQL database dump complete
--

