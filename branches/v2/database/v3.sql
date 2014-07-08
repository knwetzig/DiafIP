-- Ergänzung zur Nutzerprofilumschaltung
-- $Id$

ALTER TABLE s_auth ADD COLUMN profil character varying NOT NULL DEFAULT 'default'::character varying;
COMMENT ON COLUMN s_auth.profil IS 'Variable zur Auswahl des Nutzerprofils (Frontend)';

INSERT INTO s_strings(id, de, en, fr) VALUES (13, 'willkommen', 'welcome', 'accueil');
INSERT INTO s_strings(id, de, en, fr) VALUES (10011, 'Neuen Namen erstellen', '', '');
