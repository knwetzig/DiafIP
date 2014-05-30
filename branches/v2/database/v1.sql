--
-- PostgreSQL database cluster dump
--

\connect postgres

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Roles
--
CREATE ROLE diafuser;
ALTER ROLE diafuser WITH NOSUPERUSER INHERIT NOCREATEROLE NOCREATEDB NOLOGIN NOREPLICATION VALID UNTIL 'infinity';
CREATE ROLE "diafip-app" LOGIN NOSUPERUSER INHERIT NOCREATEDB NOCREATEROLE NOREPLICATION;

CREATE ROLE diafadmin;
ALTER ROLE diafadmin WITH NOSUPERUSER INHERIT NOCREATEROLE NOCREATEDB LOGIN NOREPLICATION PASSWORD 'md54f4e21e408e219531c3436341a3f73ef' VALID UNTIL 'infinity';


--
-- Role memberships
--

GRANT diafuser TO "diafip-app" GRANTED BY postgres;


--
-- Database creation
--

CREATE DATABASE diafip WITH TEMPLATE = template0 OWNER = diafadmin;
REVOKE ALL ON DATABASE template1 FROM PUBLIC;
REVOKE ALL ON DATABASE template1 FROM postgres;
GRANT ALL ON DATABASE template1 TO postgres;
GRANT CONNECT ON DATABASE template1 TO PUBLIC;


\connect diafip

--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: diafip; Type: COMMENT; Schema: -; Owner: diafadmin
--

COMMENT ON DATABASE diafip IS 'Datenbank für die Testumgebung von DIAFIP';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;


--
-- Name: f_bformat; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_bformat (
    format character varying NOT NULL,
    id smallint NOT NULL
);


ALTER TABLE public.f_bformat OWNER TO diafadmin;

--
-- Name: TABLE f_bformat; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_bformat IS 'Tabelle der Filmformate';


--
-- Name: f_main; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_main (
    id integer NOT NULL,
    del boolean DEFAULT false NOT NULL,
    editfrom integer NOT NULL,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    isvalid boolean DEFAULT false NOT NULL,
    bild_id integer,
    prod_jahr character varying(4),
    thema character varying,
    quellen character varying(1024),
    inhalt text,
    notiz text,
    anmerk text,
    titel character varying,
    atitel character varying,
    utitel character varying,
    sid integer,
    sfolge integer
);
ALTER TABLE ONLY f_main ALTER COLUMN prod_jahr SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_main ALTER COLUMN thema SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_main ALTER COLUMN quellen SET STORAGE EXTERNAL;


ALTER TABLE public.f_main OWNER TO diafadmin;

--
-- Name: TABLE f_main; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_main IS 'Die Stammtabelle für Filmografische und Bibliotheksdaten f_film/f_bibl
[abstract]';


--
-- Name: COLUMN f_main.del; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.del IS 'zum löschen markiert';


--
-- Name: COLUMN f_main.isvalid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.isvalid IS 'Datensätze mit "false" bedürfen einer Bearbeitung. Das trifft per Voreinstellung auf jeden neu angelegten Datensatz zu.';


--
-- Name: COLUMN f_main.bild_id; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.bild_id IS '=> bildtabelle';


--
-- Name: COLUMN f_main.thema; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.thema IS 'Freifeld für Themenschlagworte. Worte durch Komma getrennt.';


--
-- Name: COLUMN f_main.anmerk; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.anmerk IS 'Ergänzende Angaben zum Objekt (sichtbar)';


--
-- Name: f_biblio; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_biblio (
    szahl integer,
    format integer NOT NULL
)
INHERITS (f_main);
ALTER TABLE ONLY f_biblio ALTER COLUMN prod_jahr SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_biblio ALTER COLUMN thema SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_biblio ALTER COLUMN quellen SET STORAGE EXTERNAL;


ALTER TABLE public.f_biblio OWNER TO diafadmin;

--
-- Name: TABLE f_biblio; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_biblio IS 'Tabelle der bibliografischen Daten
[erbt f_main]';


--
-- Name: f_cast; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_cast (
    fid integer NOT NULL,
    pid integer NOT NULL,
    tid integer NOT NULL
);


ALTER TABLE public.f_cast OWNER TO diafadmin;

--
-- Name: TABLE f_cast; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_cast IS 'Tabelle der Besetzung in Filmen
Regisseure, Putzfrau etc....

Bei Büchern eben dann Authoren, Verleger etc..';


--
-- Name: COLUMN f_cast.fid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.fid IS 'f_film => id';


--
-- Name: COLUMN f_cast.pid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.pid IS 'p_person => id';


--
-- Name: COLUMN f_cast.tid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.tid IS 'f_taetig => taetig';


--
-- Name: f_film; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_film (
    gattung integer,
    prodtechnik integer,
    fsk integer,
    praedikat integer,
    mediaspezi integer,
    urauffuehr date,
    laenge interval,
    bildformat integer
)
INHERITS (f_main);
ALTER TABLE ONLY f_film ALTER COLUMN prod_jahr SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_film ALTER COLUMN thema SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_film ALTER COLUMN quellen SET STORAGE EXTERNAL;


ALTER TABLE public.f_film OWNER TO diafadmin;

--
-- Name: TABLE f_film; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_film IS 'Tabelle der filmografischen Daten';


--
-- Name: COLUMN f_film.gattung; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.gattung IS '=> f_gatt.gattung => s_string';


--
-- Name: COLUMN f_film.fsk; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.fsk IS 'Altersempfehlung';


--
-- Name: COLUMN f_film.mediaspezi; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.mediaspezi IS 'Bitmaske
0 = s/w-Film
1 = Stummfilm
2 = ohne Sprache';


--
-- Name: f_genre; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_genre (
    gattung smallint NOT NULL
);


ALTER TABLE public.f_genre OWNER TO diafadmin;

--
-- Name: TABLE f_genre; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_genre IS 'Verweis auf Filmgattungen';


--
-- Name: f_mediaspezi; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_mediaspezi (
    mediaspezi smallint NOT NULL
);


ALTER TABLE public.f_mediaspezi OWNER TO diafadmin;

--
-- Name: TABLE f_mediaspezi; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_mediaspezi IS 'Media-Spezifikationen';


--
-- Name: f_praed; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_praed (
    praed smallint NOT NULL
);


ALTER TABLE public.f_praed OWNER TO diafadmin;

--
-- Name: TABLE f_praed; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_praed IS '=> s__string (Verweis auf Prädikate)';


--
-- Name: f_prodtechnik; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_prodtechnik (
    beschreibung smallint NOT NULL
);


ALTER TABLE public.f_prodtechnik OWNER TO diafadmin;

--
-- Name: TABLE f_prodtechnik; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_prodtechnik IS 'Liste der eingesetzten Produktionstechniken beim Film';


--
-- Name: COLUMN f_prodtechnik.beschreibung; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_prodtechnik.beschreibung IS 'Verweis auf Stringtabelle';


--
-- Name: f_stitel; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_stitel (
    sertitel_id integer NOT NULL,
    titel character varying NOT NULL,
    descr text
);
ALTER TABLE ONLY f_stitel ALTER COLUMN titel SET STORAGE EXTERNAL;


ALTER TABLE public.f_stitel OWNER TO diafadmin;

--
-- Name: TABLE f_stitel; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_stitel IS 'Titel aller Serien';


--
-- Name: f_sertitel_id_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
--

CREATE SEQUENCE f_sertitel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.f_sertitel_id_seq OWNER TO diafadmin;

--
-- Name: f_sertitel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE f_sertitel_id_seq OWNED BY f_stitel.sertitel_id;


--
-- Name: f_sertitel_id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('f_sertitel_id_seq', 55, true);


--
-- Name: f_taetig; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE f_taetig (
    taetig smallint NOT NULL
);


ALTER TABLE public.f_taetig OWNER TO diafadmin;

--
-- Name: TABLE f_taetig; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_taetig IS 'Tätigkeiten beim Film
[nur manuell editierbar!]';


--
-- Name: id_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
--

CREATE SEQUENCE id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.id_seq OWNER TO diafadmin;

--
-- Name: id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE id_seq OWNED BY f_main.id;


--
-- Name: SEQUENCE id_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE id_seq IS 'Zähler für p_alias (Personen), i_main (Gegenstände) und f_main (Filme)';


--
-- Name: id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('id_seq', 270, true);


--
-- Name: i_main; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_main (
    id integer DEFAULT nextval('id_seq'::regclass) NOT NULL,
    notiz character varying,
    eigner integer NOT NULL,
    leihbar boolean DEFAULT false NOT NULL,
    x smallint,
    y smallint,
    kollo smallint DEFAULT 1 NOT NULL,
    akt_ort character varying,
    a_wert integer,
    oldsig character varying(10),
    herkunft integer,
    in_date date DEFAULT '1993-11-16'::date NOT NULL,
    descr text,
    rest_report text,
    bild_id integer[],
    del boolean DEFAULT false NOT NULL,
    lagerort smallint NOT NULL,
    bezeichner character varying NOT NULL,
    editfrom smallint NOT NULL,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    zu_film integer,
    isvalid boolean DEFAULT false NOT NULL
);


ALTER TABLE public.i_main OWNER TO diafadmin;

--
-- Name: TABLE i_main; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_main IS 'Abstraktes Datenmodell aller Gegenstände';


--
-- Name: COLUMN i_main.kollo; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.kollo IS 'Objekt besteht aus "kollo" Stücken';


--
-- Name: COLUMN i_main.akt_ort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.akt_ort IS 'aktueller Aufenthaltsort des Gegenstandes.
Wenn leer ist der Gegenstand am Lagerort.';


--
-- Name: COLUMN i_main.a_wert; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.a_wert IS 'Index * Konst * Zeit = Versicherungswert';


--
-- Name: COLUMN i_main.oldsig; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.oldsig IS 'Die alte Signatur á la AP-106
(feld kann nur bei Erstanlage bearbeitet werden)
Sperrfeld';


--
-- Name: COLUMN i_main.in_date; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.in_date IS 'Zugangsdatum
(Alternativ Stunde null)';


--
-- Name: COLUMN i_main.descr; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.descr IS 'Beschreibung des Gegenstandes';


--
-- Name: COLUMN i_main.rest_report; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.rest_report IS 'Restaurierungsbericht';


--
-- Name: i_3dobj; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_3dobj (
    art integer,
    z smallint
)
INHERITS (i_main);


ALTER TABLE public.i_3dobj OWNER TO diafadmin;

--
-- Name: TABLE i_3dobj; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_3dobj IS 'Alle räumlichen Gegenstände wie Puppen, Requisiten usw.';


--
-- Name: i_3dobj_art; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_3dobj_art (
    id smallint NOT NULL
);


ALTER TABLE public.i_3dobj_art OWNER TO diafadmin;

--
-- Name: COLUMN i_3dobj_art.id; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_3dobj_art.id IS 'Handpuppen, Animationspuppen usw...';


--
-- Name: i_fkop; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_fkop (
    medium smallint NOT NULL,
    material smallint,
    tonart smallint,
    fps smallint DEFAULT 24,
    laufzeit interval DEFAULT '00:00:01'::interval
)
INHERITS (i_main);


ALTER TABLE public.i_fkop OWNER TO diafadmin;

--
-- Name: TABLE i_fkop; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_fkop IS 'Tabelle der Filmmedien';


--
-- Name: COLUMN i_fkop.medium; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.medium IS 'Verweis auf Tabelle -> i_medium';


--
-- Name: COLUMN i_fkop.material; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.material IS 'Verweis auf i_material';


--
-- Name: COLUMN i_fkop.tonart; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.tonart IS 'Verweis auf i_tonart';


--
-- Name: COLUMN i_fkop.fps; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.fps IS 'frames/s';


--
-- Name: s_orte; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE s_orte (
    id integer NOT NULL,
    ort character varying NOT NULL,
    land integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.s_orte OWNER TO diafadmin;

--
-- Name: TABLE s_orte; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_orte IS 'Liste aller Orte (Städte und Gemeinden) mit Landeskennung';


--
-- Name: service_id_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
--

CREATE SEQUENCE service_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.service_id_seq OWNER TO diafadmin;

--
-- Name: service_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE service_id_seq OWNED BY s_orte.id;


--
-- Name: SEQUENCE service_id_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE service_id_seq IS 'Orte  Id-counter /
Landcounter';


--
-- Name: service_id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('service_id_seq', 86, true);


--
-- Name: i_lagerort; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_lagerort (
    nr integer DEFAULT nextval('service_id_seq'::regclass) NOT NULL,
    lagerort character varying NOT NULL
);


ALTER TABLE public.i_lagerort OWNER TO diafadmin;

--
-- Name: TABLE i_lagerort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_lagerort IS 'Liste der möglichen Lagerorte von Gegenständen';


--
-- Name: i_material; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_material (
    id smallint NOT NULL,
    material character varying NOT NULL
);


ALTER TABLE public.i_material OWNER TO diafadmin;

--
-- Name: TABLE i_material; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_material IS 'wird im Moment nur von FKop genutzt';


--
-- Name: i_medium; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_medium (
    id smallint NOT NULL,
    medium character varying NOT NULL
);


ALTER TABLE public.i_medium OWNER TO diafadmin;

--
-- Name: i_planar; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_planar (
    art integer
)
INHERITS (i_main);


ALTER TABLE public.i_planar OWNER TO diafadmin;

--
-- Name: TABLE i_planar; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_planar IS 'Alle flachen Gegenstände wie Plakate, Dokumente und Fotos';


--
-- Name: i_planar_art; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_planar_art (
    id smallint NOT NULL
);


ALTER TABLE public.i_planar_art OWNER TO diafadmin;

--
-- Name: i_tonart; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE i_tonart (
    id smallint NOT NULL,
    audiotyp character varying NOT NULL
);


ALTER TABLE public.i_tonart OWNER TO diafadmin;

--
-- Name: COLUMN i_tonart.audiotyp; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_tonart.audiotyp IS 'Lichtton, Dolby-D';


--
-- Name: m_bild; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE m_bild (
    id integer NOT NULL,
    titel character varying NOT NULL,
    descr character varying,
    file_size integer,
    img bytea NOT NULL,
    thumb bytea,
    img_x integer,
    img_y integer
);


ALTER TABLE public.m_bild OWNER TO diafadmin;

--
-- Name: m_bild_id_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
--

CREATE SEQUENCE m_bild_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER TABLE public.m_bild_id_seq OWNER TO diafadmin;

--
-- Name: m_bild_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE m_bild_id_seq OWNED BY m_bild.id;


--
-- Name: m_bild_id_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('m_bild_id_seq', 8, true);


--
-- Name: s_land; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE s_land (
    id integer DEFAULT nextval('service_id_seq'::regclass) NOT NULL,
    land character varying,
    bland character varying
);


ALTER TABLE public.s_land OWNER TO diafadmin;

--
-- Name: TABLE s_land; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_land IS 'Land-Bundesland Kombinationen
Diese Kombination bezeichnet ausdrücklich den geografischen und nicht den politischen Sachverhalt. Demnach liegt Karl-Marx-Stadt genauso wie Chemnitz in "Deutschland,Sachsen".';


--
-- Name: orte; Type: VIEW; Schema: public; Owner: diafadmin
--

CREATE VIEW orte AS
    SELECT s_orte.ort, s_land.land, s_land.bland, s_orte.id AS oid, s_land.id AS lid FROM s_orte, s_land WHERE (s_orte.land = s_land.id) ORDER BY s_orte.ort, s_land.land;


ALTER TABLE public.orte OWNER TO diafadmin;

--
-- Name: p_alias; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE p_alias (
    id integer DEFAULT nextval('id_seq'::regclass) NOT NULL,
    name character varying NOT NULL,
    notiz text
);


ALTER TABLE public.p_alias OWNER TO diafadmin;

--
-- Name: TABLE p_alias; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE p_alias IS 'Elterntabelle der Personen';


--
-- Name: COLUMN p_alias.name; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_alias.name IS 'Familien- oder Firmenname';


--




--
-- Name: p_person; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE p_person (
    notiz text,
    vname character varying DEFAULT '-'::character varying NOT NULL,
    gtag date DEFAULT '0001-01-01'::date NOT NULL,
    ttag date,
    strasse character varying,
    mail character varying,
    biogr text,
    bild integer,
    tort integer,
    gort integer,
    wort integer,
    plz character(7),
    tel character varying,
    aliases integer,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    editfrom smallint NOT NULL,
    del boolean DEFAULT false
)
INHERITS (p_alias);


ALTER TABLE public.p_person OWNER TO diafadmin;

--
-- Name: TABLE p_person; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE p_person IS 'Enthält den Bestand an natürlichen und juristischen Personen die in irgendeiner Weise mit dem DIAF in Konjunktion stehen.';


--
-- Name: COLUMN p_person.vname; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.vname IS 'Vorname(n)';


--
-- Name: COLUMN p_person.gtag; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.gtag IS 'Geburtstag / Gründungstag';


--
-- Name: COLUMN p_person.ttag; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.ttag IS 'Sterbedatum / Tag der Auflösung';


--
-- Name: COLUMN p_person.strasse; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.strasse IS 'Straße + Hausnummer und evt. Adresszusätze';


--
-- Name: COLUMN p_person.mail; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.mail IS 'eMail-Adresse';


--
-- Name: COLUMN p_person.biogr; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.biogr IS 'Biografie der Person';


--
-- Name: COLUMN p_person.bild; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.bild IS 'Index auf Bilddaten';


--
-- Name: COLUMN p_person.tort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.tort IS 'Sterbeort';


--
-- Name: COLUMN p_person.gort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.gort IS 'Geburtsort/Gründungs-';


--
-- Name: COLUMN p_person.wort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.wort IS 'Wohnort/Standort';


--
-- Name: COLUMN p_person.tel; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.tel IS 'Telefonnummer (weitere im Notizfeld vermerken)';


--
-- Name: s_auth; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE s_auth (
    username character varying(25) NOT NULL,
    password character varying NOT NULL,
    lang character(2) DEFAULT 'de'::bpchar,
    rechte integer DEFAULT 1,
    realname character varying DEFAULT '*** neuer Nutzer ***'::character varying,
    notiz text,
    uid integer NOT NULL,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    editfrom smallint DEFAULT 1 NOT NULL
);


ALTER TABLE public.s_auth OWNER TO diafadmin;

--
-- Name: TABLE s_auth; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_auth IS 'Benutzerzugänge mit ihren jeweiligen Berechtigungen';


--
-- Name: COLUMN s_auth.lang; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN s_auth.lang IS 'Sprachauswahl';


--
-- Name: s_auth_uid_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
--

CREATE SEQUENCE s_auth_uid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    MAXVALUE 32767
    CACHE 1;


ALTER TABLE public.s_auth_uid_seq OWNER TO diafadmin;

--
-- Name: s_auth_uid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE s_auth_uid_seq OWNED BY s_auth.uid;


--
-- Name: SEQUENCE s_auth_uid_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE s_auth_uid_seq IS 'Counter for Accounts';


--
-- Name: s_auth_uid_seq; Type: SEQUENCE SET; Schema: public; Owner: diafadmin
--

SELECT pg_catalog.setval('s_auth_uid_seq', 29, true);


--
-- Name: s_news; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE s_news (
    id integer DEFAULT nextval('service_id_seq'::regclass) NOT NULL,
    titel character varying(80) NOT NULL,
    inhalt text,
    editdate date DEFAULT now(),
    editfrom integer
);


ALTER TABLE public.s_news OWNER TO diafadmin;

--
-- Name: TABLE s_news; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_news IS 'Tabelle für das interne Board';


--
-- Name: s_strings; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE TABLE s_strings (
    id integer NOT NULL,
    de character varying NOT NULL,
    en character varying,
    fr character varying
);


ALTER TABLE public.s_strings OWNER TO diafadmin;

--
-- Name: TABLE s_strings; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_strings IS '
Enthält die Textpassagen für die Benutzeroberfläche (mehrsprachig)
Status-/Fehlermldg
        1 -
    450+   Datei-/Bildfehler

Labels
    500 - 9.999

Texte/Tooltips
10.000 -';



--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN del SET DEFAULT false;


--
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN editdate SET DEFAULT now();


--
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN isvalid SET DEFAULT false;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN del SET DEFAULT false;


--
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN editdate SET DEFAULT now();


--
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN isvalid SET DEFAULT false;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_main ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: sertitel_id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_stitel ALTER COLUMN sertitel_id SET DEFAULT nextval('f_sertitel_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN leihbar SET DEFAULT false;


--
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN kollo SET DEFAULT 1;


--
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN del SET DEFAULT false;


--
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN editdate SET DEFAULT now();


--
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN isvalid SET DEFAULT false;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN leihbar SET DEFAULT false;


--
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN kollo SET DEFAULT 1;


--
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN del SET DEFAULT false;


--
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN editdate SET DEFAULT now();


--
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN isvalid SET DEFAULT false;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN leihbar SET DEFAULT false;


--
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN kollo SET DEFAULT 1;


--
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN del SET DEFAULT false;


--
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN editdate SET DEFAULT now();


--
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN isvalid SET DEFAULT false;


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY m_bild ALTER COLUMN id SET DEFAULT nextval('m_bild_id_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- Name: uid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_auth ALTER COLUMN uid SET DEFAULT nextval('s_auth_uid_seq'::regclass);


--
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_orte ALTER COLUMN id SET DEFAULT nextval('service_id_seq'::regclass);


--
-- Data for Name: f_bformat; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_bformat (format, id) FROM stdin;
1:1,85	3
Cinemascope	4
1:1,35 [4:3]	1
1:1,66 [16:10]	2
1:1,77 [16:9]	5
\.


--
-- Data for Name: f_biblio; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_biblio (id, del, editfrom, editdate, isvalid, bild_id, prod_jahr, thema, quellen, inhalt, notiz, anmerk, titel, atitel, utitel, sid, sfolge, szahl, format) FROM stdin;
\.


--
-- Data for Name: f_genre; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_genre (gattung) FROM stdin;
560
561
562
568
569
674
678
679
682
686
688
696
680
565
566
697
695
681
\.


--
-- Data for Name: f_main; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_main (id, del, editfrom, editdate, isvalid, bild_id, prod_jahr, thema, quellen, inhalt, notiz, anmerk, titel, atitel, utitel, sid, sfolge) FROM stdin;
\.


--
-- Data for Name: f_mediaspezi; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_mediaspezi (mediaspezi) FROM stdin;
573
574
575
\.


--
-- Data for Name: f_praed; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_praed (praed) FROM stdin;
0
650
651
658
659
660
661
662
663
664
665
666
667
668
670
\.


--
-- Data for Name: f_prodtechnik; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_prodtechnik (beschreibung) FROM stdin;
600
601
602
603
604
605
606
607
\.


--
-- Data for Name: f_taetig; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY f_taetig (taetig) FROM stdin;
1000
1010
1040
1050
1060
1070
1080
1110
1120
1130
1140
1180
1200
1210
1220
1230
1240
1250
1270
1280
1290
1300
1310
1320
1330
1340
1360
1370
1380
1390
1400
1410
1420
1430
1440
1450
1020
1030
1090
1100
1150
1160
1170
1190
1260
1350
1460
1470
1175
1480
1490
\.


--
-- Data for Name: i_3dobj_art; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_3dobj_art (id) FROM stdin;
585
586
587
588
589
487
\.


--
-- Data for Name: i_lagerort; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

67	-- unbestimmt --
\.


--
-- Data for Name: i_main; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_main (id, notiz, eigner, leihbar, x, y, kollo, akt_ort, a_wert, oldsig, herkunft, in_date, descr, rest_report, bild_id, del, lagerort, bezeichner, editfrom, editdate, zu_film, isvalid) FROM stdin;
\.


--
-- Data for Name: i_material; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_material (id, material) FROM stdin;
5	Triazetatzellulose
10	Nitroazetatzellulose
15	Polycarbonat
\.


--
-- Data for Name: i_medium; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_medium (id, medium) FROM stdin;
10	35mm
20	16mm
30	8mm
40	Super8
50	17,5mm
60	70mm
70	VHS
80	SVHS
90	BETA
100	DigiBETA
110	CD
120	DVD
130	BD
\.


--
-- Data for Name: i_planar_art; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_planar_art (id) FROM stdin;
460
468
461
462
463
464
465
466
467
486
\.


--
-- Data for Name: i_tonart; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY i_tonart (id, audiotyp) FROM stdin;
5	LT Mono
10	LT Stereo
15	LT Dolby-SR
20	LT Dolby-D
25	MT Mono
30	MT Stereo
35	MT 4-Kanal
40	Stereo
45	Dolby-SR
50	Dolby-Digital
55	5.1
60	7.1
65	DTS
39	Mono
0	-
\.


--
-- Data for Name: m_bild; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY m_bild (id, titel, descr, file_size, img, thumb, img_x, img_y) FROM stdin;
\.


--
-- Data for Name: p_alias; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY p_alias (id, name, notiz) FROM stdin;
\.


--
-- Data for Name: p_person; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY p_person (id, name, notiz, vname, gtag, ttag, strasse, mail, biogr, bild, tort, gort, wort, plz, tel, aliases, editdate, editfrom, del) FROM stdin;
\.


--
-- Data for Name: s_auth; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY s_auth (username, password, lang, rechte, realname, notiz, uid, editdate, editfrom) FROM stdin;
test	098f6bcd4621d373cade4e832627b4f6	en	65663	Testaccount	--- NICHT ÖFFENTLICH ---	3	2013-01-25 10:22:39+01	28
gast	d4061b1486fe2da19dd578e8d970f7eb	de	1	Gastzugang	Gastzugang	4	2012-10-12 20:31:57+02	1
\.


--
-- Data for Name: s_land; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY s_land (id, land, bland) FROM stdin;
17	Polen	Niederschlesien
1	Deutschland	Sachsen
2	Deutschland	Baden-Württemberg
3	Deutschland	Bayern
4	Deutschland	Berlin
5	Deutschland	Brandenburg
6	Deutschland	Bremen
7	Deutschland	Hamburg
8	Deutschland	Hessen
9	Deutschland	Mecklenburg-Vorpommern
10	Deutschland	Niedersachsen
11	Deutschland	Nordrhein-Westfalen
12	Deutschland	Rheinland-Pfalz
13	Deutschland	Saarland
14	Deutschland	Sachsen-Anhalt
15	Deutschland	Schleswig-Holstein
16	Deutschland	Thüringen
73	Tschechien	\N
72	Österreich	\N
83	Estland	\N
0	\N	\N
\.


--
-- Data for Name: s_news; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY s_news (id, titel, inhalt, editdate, editfrom) FROM stdin;
86	Blödsinn	ist die Katze gesund, freut sich der Hund	2014-01-21	1
\.


--
-- Data for Name: s_orte; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY s_orte (id, ort, land) FROM stdin;
20	Zeisholz	1
23	Schwepnitz	1
24	Lengenfeld unterm Stein	16
19	Halle	14
32	Freiberg	1
34	Meißen	1
35	Wehlen	1
21	Dresden	1
36	Pirna	1
68	Wrocław (Breslau)	17
27	Leipzig	1
76	Kleinmachnow	5
77	Lehrte	8
79	Seiffen	1
80	Kubschütz	1
81	Moritzburg	1
82	Potsdam	4
84	Reval	83
78	Berlin	4
0	 --unbekannt--	0
\.


--
-- Data for Name: s_strings; Type: TABLE DATA; Schema: public; Owner: diafadmin
--

COPY s_strings (id, de, en, fr) FROM stdin;
100	Bitte einen gültigen Titel eingeben.	Please enter a valid title	\N
4019	Daten sichern	\N	\N
4017	neues Passwort	\N	\N
4	Fehler aufgrund fehlender/falscher Parameter	\N	\N
110	Bitte geben sie 2 gleiche Passwörter ein	\N	\N
3	Die Operation war erfolgreich	\N	\N
1	Die Daten konnten auf Grund eines internen Fehlers nicht gespeichert werden.	\N	\N
10001	Neuen Personendatensatz anlegen	\N	\N
101	Die Suchanfrage enthielt Sonderzeichen oder war leer	\N	\N
102	Die Suchanfrage brachte keine Ergebnisse	\N	\N
103	Die Datumsangabe war fehlerhaft	\N	\N
104	Die Postleitzahl kann nicht stimmen!	\N	\N
105	Die Telefonnummer ist ungültig	\N	\N
106	Die Email-adresse hat eine fehlerhafte Syntax	\N	\N
107	Geben Sie einen Namen ein	\N	\N
10002	Beispiel: +49 351 9876543<br />Erlaubt sind Plus, Ziffern und Leerzeichen	\N	\N
108	Melden Sie sich am System an, um die Seite zu nutzen.	\N	\N
109	Bitte benutzen sie nur Alphanumerische Zeichen (Ziffern und Buchstaben).	\N	\N
4015	Passwort ändern	\N	\N
4018	Passwortwiederholung	\N	\N
4016	Accountname	\N	\N
10000	Beispiele: 24.3.2002 | 24.3.02 | 20020324 | 2002-3-24<br />Falls der Monat/Tag nicht bekannt ist, jeweils durch 1 ersetzen	\N	\N
8	Fehler bei der Verarbeitung von Daten	\N	\N
1000	Regie	\N	\N
1010	Koregie	\N	\N
1040	Drehbuch	\N	\N
1050	Szenarium	\N	\N
1060	Dramaturgie	\N	\N
1080	Künstlerische Leitung	\N	\N
1110	Zeichnungen	\N	\N
1200	Titelgrafik	\N	\N
1220	Kameraassistenz	\N	\N
1240	Animation	\N	\N
1250	Psaligrafie	\N	\N
1270	Compositing	\N	\N
1280	Spezialeffekte	\N	\N
1290	Schnitt	\N	\N
1310	Komposition	\N	\N
1320	Ton	\N	\N
1330	Geräusche	\N	\N
1340	Sprecher	\N	\N
1360	Lieder	\N	\N
602	Materialanimation	\N	\N
561	Propaganda	\N	\N
560	Werbung	\N	\N
1370	Synchronisation	\N	\N
1380	Produzent	\N	\N
606	Computeranimation	\N	\N
4021	Titel löschen	\N	\N
607	Realfilm	\N	\N
4025	Bearbeiten von Titeln	\N	\N
4024	Neuanlage filmografischer Datensatz	\N	\N
4026	Neuanlage bibliografischer Datensatz	\N	\N
2	Sie haben keine ausreichende Berechtigung hierfür	\N	\N
10004	<b>ACHTUNG: </b>Sie haben dem Nutzer alle Rechte entzogen!	\N	\N
10005	<b>ACHTUNG: </b>Das setzen von Sterbedaten führt zur Löschung von Anschriften, Telefonnummern und Mailadressen, weil Tote eben keinen Wohnsitz haben.	\N	\N
10006	Der Datensatz konnte nicht gelöscht werden, weil die Daten<br />mit anderen Einträgen verknüpft sind.<br />Löschen sie erst diese und versuchen Sie es dann erneut.	\N	\N
571	Produktionstechnik	\N	\N
572	Anmerkungen	\N	\N
573	s/w-Film	\N	\N
574	Stummfilm	\N	\N
576	Produktionsjahr	\N	\N
64	reserviert fehler 6	\N	\N
32	reserviert fehler 5	\N	\N
16	reserviert fehler 4	\N	\N
256	reserviert fehler 8	\N	\N
577	Thema	\N	\N
578	Quellen	\N	\N
581	Altersempfehlung	\N	\N
582	Prädikat	\N	\N
583	Mediaspezifikation	\N	\N
584	Erstaufführung	\N	\N
4013	Datensatz bearbeiten	\N	\N
4020	Datensatz löschen	\N	\N
575	ohne&nbsp;Sprache	\N	\N
128	Datensatz bereits vorhanden	\N	\N
658	künstlerisch	\N	\N
1390	Koproduzent	\N	\N
1400	Beratung	\N	\N
1410	Darsteller	\N	\N
1420	Fotograf	\N	\N
1430	Produktionsleitung	\N	\N
1440	Redaktion	\N	\N
1450	Lizenzgeber	\N	\N
10008	<strong>Warnung: </strong>Es existieren bereits Einträge zu diesem Titel!	\N	\N
600	Zeichenanimation	\N	\N
500	Originaltitel	\N	\N
501	Untertitel	\N	\N
503	Arbeitstitel	\N	\N
505	Folge	\N	\N
509	gest.	\N	\N
511	tel	\N	\N
512	eMail	\N	\N
514	Notiz	\N	\N
516	Vorname	\N	\N
510	Anschrift	\N	\N
506	Beschreibung	\N	\N
504	Serientitel	\N	\N
502	geb.	\N	\N
513	Biografie	\N	\N
515	Künstlername	\N	\N
517	Name	\N	\N
1210	Kamera	\N	\N
4000	Gegenstände	Objects	Objets
4004	anmelden	login	enregistrer
4007	Verwaltung	\N	\N
4011	Suchmaske	\N	\N
4014	in	\N	\N
4002	Kopien	\N	\N
4003	Personen	Persons	Personnes
4006	Einstellungen	Settings	Réglages
4008	Filmografie	Filmography	Filmographie
4010	Titelverzeichnis	\N	\N
4012	Personenverwaltung	\N	\N
4022	Titel bearbeiten	\N	\N
4023	Neuanlage Titel	\N	\N
569	Lehr-/Instruktionsfilm	\N	\N
580	Laufzeit	Processtime	\N
0	-- kein Eintrag --	-- no message --	-- pas d'entrée --
4001	Filme	Films	Films
579	Klassifizierung	\N	\N
4005	abmelden	logout	déconnecter
1490	Auftraggeber	\N	\N
4027	Stammdaten	\N	\N
608	Bildformat	\N	\N
453	Upload-Verzeichnis nicht gefunden	Upload folder not found	\N
454	Kann hochgeladene Datei nicht speichern	Unable to write uploaded file	\N
456	Unbekannter Fehler	Unknown error	\N
457	Bild nicht in hochgeladen Daten gefunden	Image not found in uploaded data	\N
458	Datei ist keine hochgeladene Datei	File is not an uploaded file	\N
459	Datei ist keine Bilddatei	File is not an image	\N
450	Die hochgeladene Datei überschreitet die festgelegte Größe. 	Image is too large	\N
451	Die Datei wurde nur teilweise hochgeladen.	Image was only partially uploaded	\N
452	Keine Datei hochgeladen	No image was uploaded	\N
455	Systemkomponente hat das hochladen gestoppt	Upload failed due to extension	\N
659	volksbildend	\N	\N
660	Lehrfilm	\N	\N
661	künstlerisch besonders wertvoll	\N	\N
662	staatspolitisch wertvoll	\N	\N
663	kulturell wertvoll	\N	\N
664	staatspolitisch und künstlerisch besonders wertvoll	\N	\N
666	staatspolitisch besonders wertvoll, künstlerisch besonders wertvoll	\N	\N
667	volkstümlich wertvoll	\N	\N
668	jugendwert	\N	\N
650	Besonders wertvoll	\N	\N
651	Wertvoll	\N	\N
670	Sehenswert	\N	\N
1100	Gesamtdesign	\N	\N
688	Western	\N	\N
1260	Puppenführung	\N	\N
462	Foto	\N	\N
461	Grafik	\N	\N
460	Dokument	\N	\N
464	Phasenzeichnung	\N	\N
465	Plakat	\N	\N
466	Silhouettenfigur	\N	\N
467	Urkunde	\N	\N
468	Flachfigur	\N	\N
469	Breite	\N	\N
470	Höhe	\N	\N
471	Tiefe	\N	\N
472	Lagerort	\N	\N
473	Eigentümer	\N	\N
474	leihbar	\N	\N
475	Stückzahl	\N	\N
476	akt. Lagerort	\N	\N
477	Versicherungswert	\N	\N
478	Wert	\N	\N
479	alte Signatur	\N	\N
480	Herkunft	\N	\N
481	Eingangsdatum	\N	\N
482	Zustandsbericht	\N	\N
483	Gegenstandstyp	\N	\N
4029	Bezeichnung	\N	\N
4030	Art	\N	\N
4031	Neuwert	\N	\N
484	Maße	\N	\N
111	Maßangaben sind unvollständig	\N	\N
5	Titel	\N	\N
679	Erotik	\N	\N
682	Spannung	\N	\N
696	Märchen	\N	\N
686	Science-Fiction	\N	\N
463	Entwurf	\N	\N
486	Hintergrund	\N	\N
586	Requisiten	\N	\N
587	Preise / Auszeichnungen	\N	\N
6	Der Datensatz konnte nicht gelöscht werden da er noch mit anderen Daten verbunden ist.	\N	\N
601	Puppenanimation	\N	\N
603	Puppenspiel	\N	\N
604	Silhouettenanimation	\N	\N
605	Flachfigurenanimation	\N	\N
665	staatspolitisch wertvoll, künstlerisch wertvoll	\N	\N
10010	Gegenstand mit Eintrag sychronisiert, alle Daten vollständig	\N	\N
681	Animadoc	\N	\N
674	Cartoon	\N	\N
565	Das andere Kino	\N	\N
566	Experimenteller Film	\N	\N
697	Kinderfilm	\N	\N
678	Multimedia	\N	\N
562	Musik	\N	\N
1300	Musikbearbeitung	\N	\N
1350	Musiker/Gesang	\N	\N
568	Satire	\N	\N
695	Zeitgeschichte	\N	\N
1470	Ausführende Firma	\N	\N
1180	Bauten Design	\N	\N
1190	Bauten Ausführung	\N	\N
1230	Chefanimator	\N	\N
1070	Dialoge/Texte	\N	\N
1120	Figurendesign	\N	\N
1130	Figurenbau	\N	\N
1170	Hintergrunddesign	\N	\N
1175	Hintergrund Ausführung	\N	\N
1030	Idee/Stoff	\N	\N
1150	Kostüme	\N	\N
1160	Modelling	\N	\N
1020	Regieassistenz	\N	\N
1140	Rigging	\N	\N
1090	Technische Leitung	\N	\N
1460	Verleih	\N	\N
588	Technik	\N	\N
589	sonstiges	\N	\N
10009	alle Daten vollständig und überprüft	\N	\N
487	Handpuppe	\N	\N
585	Animationspuppe	\N	\N
4033	Nutzerverwaltung	\N	\N
12	Französisch	French	Français
4034	Neuen Nutzer anlegen	\N	\N
4035	Nutzername	Username	\N
10	Deutsch	German	Allemand
11	Englisch	English	Anglais
10007	Eingabe als absoluter Wert: <b>2:39:10</b> oder als relativer Wert, wobei die Werte in den unterschiedlichen Einheiten automatisch unter Beachtung der Vorzeichen zusammengezählt werden. Beispiel: <b>1h 99m 10s</b>.<br /><br /><strong>Umrechnungsfaktor: </strong>1 Meter Film (Normalfilm 35mm) entsprechen 2,193 Sekunden.	\N	\N
4036	Übersetzung	Translation	\N
1480	Hersteller	\N	\N
4037	Nachrichten	\N	\N
698	Produktionsland	\N	\N
4009	Statistik	Statistic	Statistiques
680	Abstrakter Film	\N	\N
4032	Objekt 3D	3D Object	Objet 3D
488	Trägermaterial	\N	\N
489	Bilder/s	\N	\N
490	Medium	\N	\N
491	Tonverfahren	\N	\N
4028	Objekt 2D	2D Object	Objet 2D
4038	Medien	\N	\N
485	<span style='color:red'>nicht leihbar</span>	\N	\N
\.



--
-- Name: f_bformat_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_bformat
    ADD CONSTRAINT f_bformat_pkey PRIMARY KEY (id);


--
-- Name: f_biblio_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_biblio
    ADD CONSTRAINT f_biblio_pkey PRIMARY KEY (id);


--
-- Name: f_cast_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pkey PRIMARY KEY (fid, pid, tid);


--
-- Name: f_film_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_pkey PRIMARY KEY (id);


--
-- Name: f_gattung_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_genre
    ADD CONSTRAINT f_gattung_pkey PRIMARY KEY (gattung);


--
-- Name: f_main_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_pkey PRIMARY KEY (id);


--
-- Name: f_praed_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_pkey PRIMARY KEY (praed);


--
-- Name: f_prodtechnik_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_prodtechnik
    ADD CONSTRAINT f_prodtechnik_pkey PRIMARY KEY (beschreibung);


--
-- Name: f_stitel_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_pkey PRIMARY KEY (sertitel_id);


--
-- Name: f_stitel_titel_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_titel_key UNIQUE (titel);


--
-- Name: f_taetig_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_pkey PRIMARY KEY (taetig);


--
-- Name: i_3dobj_art_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_3dobj_art
    ADD CONSTRAINT i_3dobj_art_pkey PRIMARY KEY (id);


--
-- Name: i_3dobj_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_3dobj
    ADD CONSTRAINT i_3dobj_pkey PRIMARY KEY (id);


--
-- Name: i_fkop_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_pkey PRIMARY KEY (id);


--
-- Name: i_material_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_material
    ADD CONSTRAINT i_material_pkey PRIMARY KEY (id);


--
-- Name: i_medium_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_medium
    ADD CONSTRAINT i_medium_pkey PRIMARY KEY (id);


--
-- Name: i_planar_art_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_planar_art
    ADD CONSTRAINT i_planar_art_pkey PRIMARY KEY (id);


--
-- Name: i_planar_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_planar
    ADD CONSTRAINT i_planar_pkey PRIMARY KEY (id);


--
-- Name: i_tonart_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_tonart
    ADD CONSTRAINT i_tonart_pkey PRIMARY KEY (id);


--
-- Name: idx; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT idx PRIMARY KEY (id);


--
-- Name: images_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY m_bild
    ADD CONSTRAINT images_pkey PRIMARY KEY (id);


--
-- Name: lagerort_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY i_lagerort
    ADD CONSTRAINT lagerort_pkey PRIMARY KEY (nr);


--
-- Name: mediaspezi_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY f_mediaspezi
    ADD CONSTRAINT mediaspezi_pkey PRIMARY KEY (mediaspezi);


--
-- Name: ort_idx; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT ort_idx PRIMARY KEY (id);


--
-- Name: p_alias_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY p_alias
    ADD CONSTRAINT p_alias_pkey PRIMARY KEY (id);



--
-- Name: p_person_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_pkey PRIMARY KEY (id);


--
-- Name: p_person_unique; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_unique UNIQUE (vname, name, gtag);


--
-- Name: s_auth_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_pkey PRIMARY KEY (uid);


--
-- Name: s_auth_username_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_username_key UNIQUE (username);


--
-- Name: s_land_land_bland_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_land_bland_key UNIQUE (land, bland);


--
-- Name: s_land_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_pkey PRIMARY KEY (id);


--
-- Name: s_news_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_pkey PRIMARY KEY (id);


--
-- Name: s_orte_ort_land_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT s_orte_ort_land_key UNIQUE (ort, land);


--
-- Name: str_id; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT str_id PRIMARY KEY (id);


--
-- Name: uniq_de_text; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace:
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT uniq_de_text UNIQUE (de);


--
-- Name: fki_gort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE INDEX fki_gort_fidx ON p_person USING btree (gort);


--
-- Name: fki_tort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE INDEX fki_tort_fidx ON p_person USING btree (tort);


--
-- Name: fki_uid_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE INDEX fki_uid_fidx ON p_person USING btree (editfrom);


--
-- Name: fki_wort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE INDEX fki_wort_fidx ON p_person USING btree (wort);


--
-- Name: name_idx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace:
--

CREATE INDEX name_idx ON p_alias USING btree (name);


--
-- Name: f_biblio_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio
    ADD CONSTRAINT f_biblio_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid) MATCH FULL;


--
-- Name: f_cast_pid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pid_fkey FOREIGN KEY (pid) REFERENCES p_person(id) MATCH FULL;


--
-- Name: f_cast_tid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_tid_fkey FOREIGN KEY (tid) REFERENCES f_taetig(taetig);


--
-- Name: f_film_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid) MATCH FULL;


--
-- Name: f_film_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_gattung_fkey FOREIGN KEY (gattung) REFERENCES f_genre(gattung) MATCH FULL;


--
-- Name: f_film_praedikat_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_praedikat_fkey FOREIGN KEY (praedikat) REFERENCES f_praed(praed);


--
-- Name: f_gattung_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_genre
    ADD CONSTRAINT f_gattung_gattung_fkey FOREIGN KEY (gattung) REFERENCES s_strings(id) MATCH FULL;


--
-- Name: f_main_sid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_sid_fkey FOREIGN KEY (sid) REFERENCES f_stitel(sertitel_id) MATCH FULL;


--
-- Name: f_mediaspezi_mediaspezi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_mediaspezi
    ADD CONSTRAINT f_mediaspezi_mediaspezi_fkey FOREIGN KEY (mediaspezi) REFERENCES s_strings(id);


--
-- Name: f_praed_praed_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_praed_fkey FOREIGN KEY (praed) REFERENCES s_strings(id);


--
-- Name: f_prodtechnik_beschreibung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_prodtechnik
    ADD CONSTRAINT f_prodtechnik_beschreibung_fkey FOREIGN KEY (beschreibung) REFERENCES s_strings(id) MATCH FULL;


--
-- Name: f_taetig_taetig_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_taetig_fkey FOREIGN KEY (taetig) REFERENCES s_strings(id);


--
-- Name: gort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT gort_fidx FOREIGN KEY (gort) REFERENCES s_orte(id);


--
-- Name: i_3dobj_art_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj_art
    ADD CONSTRAINT i_3dobj_art_id_fkey FOREIGN KEY (id) REFERENCES s_strings(id);


--
-- Name: i_3dobj_art_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj
    ADD CONSTRAINT i_3dobj_art_id_fkey FOREIGN KEY (art) REFERENCES i_3dobj_art(id);


--
-- Name: i_fkop_material_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_material_fkey FOREIGN KEY (material) REFERENCES i_material(id);


--
-- Name: i_fkop_medium_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_medium_fkey FOREIGN KEY (medium) REFERENCES i_medium(id);


--
-- Name: i_fkop_tonart_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_tonart_fkey FOREIGN KEY (tonart) REFERENCES i_tonart(id);


--
-- Name: i_objekt_eigner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT i_objekt_eigner_fkey FOREIGN KEY (eigner) REFERENCES p_person(id);


--
-- Name: i_objekt_herkunft_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT i_objekt_herkunft_fkey FOREIGN KEY (herkunft) REFERENCES p_person(id);


--
-- Name: i_planar_art_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar
    ADD CONSTRAINT i_planar_art_fkey FOREIGN KEY (art) REFERENCES i_planar_art(id);


--
-- Name: s_auth_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- Name: s_news_autor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_autor_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- Name: s_orte_land_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT s_orte_land_fkey FOREIGN KEY (land) REFERENCES s_land(id);


--
-- Name: tort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT tort_fidx FOREIGN KEY (tort) REFERENCES s_orte(id);


--
-- Name: uid_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT uid_fidx FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- Name: wort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT wort_fidx FOREIGN KEY (wort) REFERENCES s_orte(id);


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO diafadmin;
GRANT USAGE ON SCHEMA public TO PUBLIC;



--
-- Name: f_bformat; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_bformat FROM PUBLIC;
REVOKE ALL ON TABLE f_bformat FROM diafadmin;
GRANT ALL ON TABLE f_bformat TO diafadmin;
GRANT SELECT ON TABLE f_bformat TO PUBLIC;


--
-- Name: f_main; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_main FROM PUBLIC;
REVOKE ALL ON TABLE f_main FROM diafadmin;
GRANT ALL ON TABLE f_main TO diafadmin;
GRANT SELECT ON TABLE f_main TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_main TO diafuser;


--
-- Name: f_biblio; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_biblio FROM PUBLIC;
REVOKE ALL ON TABLE f_biblio FROM diafadmin;
GRANT ALL ON TABLE f_biblio TO diafadmin;
GRANT SELECT ON TABLE f_biblio TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_biblio TO diafuser;


--
-- Name: f_cast; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_cast FROM PUBLIC;
REVOKE ALL ON TABLE f_cast FROM diafadmin;
GRANT ALL ON TABLE f_cast TO diafadmin;
GRANT SELECT ON TABLE f_cast TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_cast TO diafuser;


--
-- Name: f_film; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_film FROM PUBLIC;
REVOKE ALL ON TABLE f_film FROM diafadmin;
GRANT ALL ON TABLE f_film TO diafadmin;
GRANT SELECT ON TABLE f_film TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_film TO diafuser;


--
-- Name: f_genre; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_genre FROM PUBLIC;
REVOKE ALL ON TABLE f_genre FROM diafadmin;
GRANT ALL ON TABLE f_genre TO diafadmin;
GRANT SELECT ON TABLE f_genre TO PUBLIC;


--
-- Name: f_mediaspezi; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_mediaspezi FROM PUBLIC;
REVOKE ALL ON TABLE f_mediaspezi FROM diafadmin;
GRANT ALL ON TABLE f_mediaspezi TO diafadmin;
GRANT SELECT ON TABLE f_mediaspezi TO PUBLIC;


--
-- Name: f_praed; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_praed FROM PUBLIC;
REVOKE ALL ON TABLE f_praed FROM diafadmin;
GRANT ALL ON TABLE f_praed TO diafadmin;
GRANT SELECT ON TABLE f_praed TO PUBLIC;


--
-- Name: f_prodtechnik; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_prodtechnik FROM PUBLIC;
REVOKE ALL ON TABLE f_prodtechnik FROM diafadmin;
GRANT ALL ON TABLE f_prodtechnik TO diafadmin;
GRANT SELECT ON TABLE f_prodtechnik TO PUBLIC;


--
-- Name: f_stitel; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_stitel FROM PUBLIC;
REVOKE ALL ON TABLE f_stitel FROM diafadmin;
GRANT ALL ON TABLE f_stitel TO diafadmin;
GRANT SELECT ON TABLE f_stitel TO PUBLIC;


--
-- Name: f_sertitel_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE f_sertitel_id_seq TO diafadmin;
GRANT SELECT ON SEQUENCE f_sertitel_id_seq TO PUBLIC;
GRANT ALL ON SEQUENCE f_sertitel_id_seq TO diafuser;


--
-- Name: f_taetig; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_taetig FROM PUBLIC;
REVOKE ALL ON TABLE f_taetig FROM diafadmin;
GRANT ALL ON TABLE f_taetig TO diafadmin;
GRANT SELECT ON TABLE f_taetig TO PUBLIC;


--
-- Name: id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE id_seq TO diafadmin;
GRANT ALL ON SEQUENCE id_seq TO diafuser;


--
-- Name: i_main; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_main FROM PUBLIC;
REVOKE ALL ON TABLE i_main FROM diafadmin;
GRANT ALL ON TABLE i_main TO diafadmin;
GRANT SELECT ON TABLE i_main TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_main TO diafuser;


--
-- Name: i_3dobj; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_3dobj FROM PUBLIC;
REVOKE ALL ON TABLE i_3dobj FROM diafadmin;
GRANT ALL ON TABLE i_3dobj TO diafadmin;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_3dobj TO diafuser;
GRANT SELECT ON TABLE i_3dobj TO PUBLIC;


--
-- Name: i_3dobj_art; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_3dobj_art FROM PUBLIC;
REVOKE ALL ON TABLE i_3dobj_art FROM diafadmin;
GRANT ALL ON TABLE i_3dobj_art TO diafadmin;
GRANT SELECT ON TABLE i_3dobj_art TO PUBLIC;


--
-- Name: i_fkop; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_fkop FROM PUBLIC;
REVOKE ALL ON TABLE i_fkop FROM diafadmin;
GRANT ALL ON TABLE i_fkop TO diafadmin;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_fkop TO diafuser;
GRANT SELECT ON TABLE i_fkop TO PUBLIC;


--
-- Name: s_orte; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_orte FROM PUBLIC;
REVOKE ALL ON TABLE s_orte FROM diafadmin;
GRANT ALL ON TABLE s_orte TO diafadmin;
GRANT SELECT ON TABLE s_orte TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_orte TO diafuser;


--
-- Name: service_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE service_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE service_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE service_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE service_id_seq TO diafuser;


--
-- Name: i_lagerort; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_lagerort FROM PUBLIC;
REVOKE ALL ON TABLE i_lagerort FROM diafadmin;
GRANT ALL ON TABLE i_lagerort TO diafadmin;
GRANT SELECT ON TABLE i_lagerort TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_lagerort TO diafuser;


--
-- Name: i_material; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_material FROM PUBLIC;
REVOKE ALL ON TABLE i_material FROM diafadmin;
GRANT ALL ON TABLE i_material TO diafadmin;
GRANT SELECT ON TABLE i_material TO PUBLIC;


--
-- Name: i_medium; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_medium FROM PUBLIC;
REVOKE ALL ON TABLE i_medium FROM diafadmin;
GRANT ALL ON TABLE i_medium TO diafadmin;
GRANT SELECT ON TABLE i_medium TO PUBLIC;


--
-- Name: i_planar; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_planar FROM PUBLIC;
REVOKE ALL ON TABLE i_planar FROM diafadmin;
GRANT ALL ON TABLE i_planar TO diafadmin;
GRANT SELECT ON TABLE i_planar TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_planar TO diafuser;


--
-- Name: i_planar_art; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_planar_art FROM PUBLIC;
REVOKE ALL ON TABLE i_planar_art FROM diafadmin;
GRANT ALL ON TABLE i_planar_art TO diafadmin;
GRANT SELECT ON TABLE i_planar_art TO PUBLIC;


--
-- Name: i_tonart; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_tonart FROM PUBLIC;
REVOKE ALL ON TABLE i_tonart FROM diafadmin;
GRANT ALL ON TABLE i_tonart TO diafadmin;
GRANT SELECT ON TABLE i_tonart TO PUBLIC;


--
-- Name: m_bild; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE m_bild FROM PUBLIC;
REVOKE ALL ON TABLE m_bild FROM diafadmin;
GRANT ALL ON TABLE m_bild TO diafadmin;
GRANT SELECT ON TABLE m_bild TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE m_bild TO diafuser;


--
-- Name: m_bild_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE m_bild_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE m_bild_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafuser;


--
-- Name: s_land; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_land FROM PUBLIC;
REVOKE ALL ON TABLE s_land FROM diafadmin;
GRANT ALL ON TABLE s_land TO diafadmin;
GRANT SELECT ON TABLE s_land TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_land TO diafuser;


--
-- Name: orte; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE orte FROM PUBLIC;
REVOKE ALL ON TABLE orte FROM diafadmin;
GRANT ALL ON TABLE orte TO diafadmin;
GRANT SELECT ON TABLE orte TO PUBLIC;


--
-- Name: p_alias; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE p_alias FROM PUBLIC;
REVOKE ALL ON TABLE p_alias FROM diafadmin;
GRANT ALL ON TABLE p_alias TO diafadmin;
GRANT SELECT ON TABLE p_alias TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE p_alias TO diafuser;




--
-- Name: p_person; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE p_person FROM PUBLIC;
REVOKE ALL ON TABLE p_person FROM diafadmin;
GRANT ALL ON TABLE p_person TO diafadmin;
GRANT SELECT ON TABLE p_person TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE p_person TO diafuser;


--
-- Name: s_auth; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_auth FROM PUBLIC;
REVOKE ALL ON TABLE s_auth FROM diafadmin;
GRANT ALL ON TABLE s_auth TO diafadmin;
GRANT SELECT ON TABLE s_auth TO PUBLIC;
GRANT SELECT,UPDATE ON TABLE s_auth TO diafuser;


--
-- Name: s_auth_uid_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafuser;


--
-- Name: s_news; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_news FROM PUBLIC;
REVOKE ALL ON TABLE s_news FROM diafadmin;
GRANT ALL ON TABLE s_news TO diafadmin;
GRANT SELECT ON TABLE s_news TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_news TO diafuser;


--
-- Name: s_strings; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_strings FROM PUBLIC;
REVOKE ALL ON TABLE s_strings FROM diafadmin;
GRANT ALL ON TABLE s_strings TO diafadmin;
GRANT SELECT ON TABLE s_strings TO PUBLIC;
GRANT SELECT,UPDATE ON TABLE s_strings TO diafuser;


--
-- Name: s_strings.en; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL(en) ON TABLE s_strings FROM PUBLIC;
REVOKE ALL(en) ON TABLE s_strings FROM diafadmin;
GRANT UPDATE(en) ON TABLE s_strings TO diafuser;


--
-- Name: s_strings.fr; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL(fr) ON TABLE s_strings FROM PUBLIC;
REVOKE ALL(fr) ON TABLE s_strings FROM diafadmin;
GRANT UPDATE(fr) ON TABLE s_strings TO diafuser;


--
-- PostgreSQL database dump complete
--

\connect postgres

--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: postgres; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON DATABASE postgres IS 'default administrative connection database';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: adminpack; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS adminpack WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION adminpack; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION adminpack IS 'administrative functions for PostgreSQL';


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- Name: DEFAULT PRIVILEGES FOR SEQUENCES; Type: DEFAULT ACL; Schema: -; Owner: diafadmin
--

ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin REVOKE ALL ON SEQUENCES  FROM PUBLIC;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin REVOKE ALL ON SEQUENCES  FROM diafadmin;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin GRANT ALL ON SEQUENCES  TO diafadmin;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin GRANT ALL ON SEQUENCES  TO diafuser;


--
-- Name: DEFAULT PRIVILEGES FOR TABLES; Type: DEFAULT ACL; Schema: -; Owner: diafadmin
--

ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin REVOKE ALL ON TABLES  FROM PUBLIC;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin REVOKE ALL ON TABLES  FROM diafadmin;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin GRANT ALL ON TABLES  TO diafadmin;
ALTER DEFAULT PRIVILEGES FOR ROLE diafadmin GRANT SELECT,INSERT,DELETE,UPDATE ON TABLES  TO diafuser;


--
-- PostgreSQL database dump complete
--

\connect template1

--
-- PostgreSQL database dump
--

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- Name: template1; Type: COMMENT; Schema: -; Owner: postgres
--

COMMENT ON DATABASE template1 IS 'default template database';


--
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner:
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner:
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


--
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO PUBLIC;


--
-- PostgreSQL database dump complete
--

--
-- PostgreSQL database cluster dump complete
--

