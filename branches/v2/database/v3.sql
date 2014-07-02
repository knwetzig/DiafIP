-- Ergänzung zur Nutzerprofilumschaltung
-- $Id$

ALTER TABLE s_auth ADD COLUMN profil character varying NOT NULL DEFAULT 'default'::character varying;
COMMENT ON COLUMN s_auth.profil IS 'Variable zur Auswahl des Nutzerprofils (Frontend)';
