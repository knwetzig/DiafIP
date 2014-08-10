-- $Id$

-- ---------------------------------------------------------------------------
--              Ergänzung zur Nutzerprofilumschaltung
-- ---------------------------------------------------------------------------
ALTER TABLE s_auth ADD COLUMN profil character varying NOT NULL DEFAULT 'default'::character varying;
COMMENT ON COLUMN s_auth.profil IS 'Variable zur Auswahl des Nutzerprofils (Frontend)';

INSERT INTO s_strings(id, de, en, fr) VALUES (13, 'willkommen', 'welcome', 'accueil');
INSERT INTO s_strings(id, de, en, fr) VALUES (10011, 'Neuen Namen erstellen', '', '');

-- ---------------------------------------------------------------------------
--              Neuanlage f_main2 / f_film2
-- ---------------------------------------------------------------------------
CREATE TABLE f_main2
(
-- Geerbt from table entity:  id integer NOT NULL DEFAULT nextval('entity_id_seq'::regclass),
-- Geerbt from table entity:  bereich character(1) NOT NULL,
-- Geerbt from table entity:  descr text,
-- Geerbt from table entity:  bilder integer[],
-- Geerbt from table entity:  notiz text,
-- Geerbt from table entity:  isvalid boolean DEFAULT false,
-- Geerbt from table entity:  del boolean DEFAULT false,
-- Geerbt from table entity:  editfrom smallint NOT NULL,
-- Geerbt from table entity:  editdate timestamp with time zone NOT NULL DEFAULT now(),
  titel character varying,
  atitel character varying,
  utitel character varying,
  sid integer,
  sfolge integer,
  prod_jahr character varying(4),
  anmerk text, -- Ergänzende Angaben zum Objekt (public)
  quellen character varying(1024),
  thema character varying[], -- Freifeld für Themenschlagworte
  CONSTRAINT f_main2_pkey PRIMARY KEY (id),
  CONSTRAINT f_main2_sid_fkey FOREIGN KEY (sid)
      REFERENCES f_stitel (sertitel_id) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (entity)
WITH (
  OIDS=FALSE
);
ALTER TABLE f_main2
  OWNER TO diafadmin;
GRANT ALL ON TABLE f_main2 TO diafadmin;
GRANT SELECT ON TABLE f_main2 TO public;
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE f_main2 TO diafuser;
COMMENT ON TABLE f_main2
  IS 'Die Stammtabelle für Filmografische und Bibliotheksdaten f_film/f_bibl [abstract]';
COMMENT ON COLUMN f_main2.anmerk IS 'Ergänzende Angaben zum Objekt (public)';
COMMENT ON COLUMN f_main2.thema IS 'Freifeld für Themenschlagworte';

CREATE TABLE f_film2
(
-- Geerbt from table f_main2:  id integer NOT NULL DEFAULT nextval('entity_id_seq'::regclass),
-- Geerbt from table f_main2:  bereich character(1) NOT NULL,
-- Geerbt from table f_main2:  descr text,
-- Geerbt from table f_main2:  bilder integer[],
-- Geerbt from table f_main2:  notiz text,
-- Geerbt from table f_main2:  isvalid boolean DEFAULT false,
-- Geerbt from table f_main2:  del boolean DEFAULT false,
-- Geerbt from table f_main2:  editfrom smallint NOT NULL,
-- Geerbt from table f_main2:  editdate timestamp with time zone NOT NULL DEFAULT now(),
-- Geerbt from table f_main2:  titel character varying,
-- Geerbt from table f_main2:  atitel character varying,
-- Geerbt from table f_main2:  utitel character varying,
-- Geerbt from table f_main2:  sid integer,
-- Geerbt from table f_main2:  sfolge integer,
-- Geerbt from table f_main2:  prod_jahr character varying(4),
-- Geerbt from table f_main2:  anmerk text,
-- Geerbt from table f_main2:  quellen character varying(1024),
-- Geerbt from table f_main2:  thema character varying[],
  gattung integer, -- => f_gatt.gattung => s_string
  prodtechnik integer,
  fsk integer, -- Altersempfehlung
  praedikat integer,
  mediaspezi integer, -- Bitmaske...
  urauffuehr date,
  laenge interval,
  bildformat integer,
  CONSTRAINT f_film2_pkey PRIMARY KEY (id),
  CONSTRAINT f_film_gattung_fkey FOREIGN KEY (gattung)
      REFERENCES f_genre (gattung) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION,
  CONSTRAINT f_film_praedikat_fkey FOREIGN KEY (praedikat)
      REFERENCES f_praed (praed) MATCH FULL
      ON UPDATE NO ACTION ON DELETE NO ACTION
)
INHERITS (f_main2)
WITH (
  OIDS=FALSE
);
ALTER TABLE f_film2
  OWNER TO diafadmin;
GRANT ALL ON TABLE f_film2 TO diafadmin;
GRANT SELECT ON TABLE f_film2 TO public;
GRANT SELECT, UPDATE, INSERT, DELETE ON TABLE f_film2 TO diafuser;
COMMENT ON TABLE f_film2
  IS 'Tabelle der filmografischen Daten';
COMMENT ON COLUMN f_film2.gattung IS '=> f_gatt.gattung => s_string';
COMMENT ON COLUMN f_film2.fsk IS 'Altersempfehlung';
COMMENT ON COLUMN f_film2.mediaspezi IS 'Bitmaske
0 = s/w-Film
1 = Stummfilm
2 = ohne Sprache';



-- ----------------------------------------------------------------------------
--              Alten Datenbestand übertragen
-- ----------------------------------------------------------------------------
INSERT INTO f_film2 (
    id, bereich, editfrom, editdate,
    prod_jahr, quellen, descr,
    notiz, anmerk, titel,
    atitel, utitel, sid,
    sfolge, gattung,prodtechnik,
    fsk, praedikat, mediaspezi,
    urauffuehr, laenge, bildformat
)
SELECT
    f_film.id, 'F',f_film.editfrom, f_film.editdate,
    f_film.prod_jahr, f_film.quellen, f_film.inhalt AS descr,
    f_film.notiz, f_film.anmerk, f_film.titel,
    f_film.atitel, f_film.utitel, f_film.sid,
    f_film.sfolge, f_film.gattung, f_film.prodtechnik,
    f_film.fsk, f_film.praedikat, f_film.mediaspezi,
    f_film.urauffuehr, f_film.laenge, f_film.bildformat
FROM
    public.f_film;

-- ----------------------------------------------------------------------------
--              Drop alte Bereiche
-- ----------------------------------------------------------------------------
DROP TABLE f_film CASCADE;
DROP TABLE f_main CASCADE;