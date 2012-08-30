--
-- PostgreSQL database dump
--

-- Dumped from database version 9.0.4
-- Dumped by pg_dump version 9.0.4
-- Started on 2012-08-30 14:11:08 CEST
-- $Id:$

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = off;
SET check_function_bodies = false;
SET client_min_messages = warning;
SET escape_string_warning = off;

SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 1581 (class 1259 OID 17230)
-- Dependencies: 6
-- Name: f_cast; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_cast (
    fid integer NOT NULL,
    pid integer NOT NULL,
    tid integer NOT NULL
);


--
-- TOC entry 1974 (class 0 OID 0)
-- Dependencies: 1581
-- Name: TABLE f_cast; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_cast IS 'Tabelle der Besetzung in Filmen
Regisseure, Putzfrau etc....

Bei Büchern eben dann Authoren, Verleger etc..';


--
-- TOC entry 1975 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN f_cast.fid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_cast.fid IS 'f_film => id';


--
-- TOC entry 1976 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN f_cast.pid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_cast.pid IS 'p_person => id';


--
-- TOC entry 1977 (class 0 OID 0)
-- Dependencies: 1581
-- Name: COLUMN f_cast.tid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_cast.tid IS 'f_taetig => taetig';


--
-- TOC entry 1586 (class 1259 OID 17316)
-- Dependencies: 1894 1895 1896 6
-- Name: f_main; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_main (
    id integer NOT NULL,
    del boolean DEFAULT false NOT NULL,
    editfrom integer NOT NULL,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    isvalid boolean DEFAULT false NOT NULL,
    titel_id integer NOT NULL,
    bild_id integer,
    prod_jahr character varying(4),
    thema character varying,
    quellen character varying(1024),
    inhalt text,
    notiz text
);
ALTER TABLE ONLY f_main ALTER COLUMN prod_jahr SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_main ALTER COLUMN thema SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_main ALTER COLUMN quellen SET STORAGE EXTERNAL;


--
-- TOC entry 1979 (class 0 OID 0)
-- Dependencies: 1586
-- Name: TABLE f_main; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_main IS 'Die Stammtabelle für Filmografische und Bibliotheksdaten f_film/f_bibl
[abstract]';


--
-- TOC entry 1980 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN f_main.del; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_main.del IS 'zum löschen markiert';


--
-- TOC entry 1981 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN f_main.isvalid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_main.isvalid IS 'Datensätze mit "false" bedürfen einer Bearbeitung. Das trifft per Voreinstellung auf jeden neu angelegten Datensatz zu.';


--
-- TOC entry 1982 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN f_main.titel_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_main.titel_id IS '=> f_titel.id';


--
-- TOC entry 1983 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN f_main.bild_id; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_main.bild_id IS '=> bildtabelle';


--
-- TOC entry 1984 (class 0 OID 0)
-- Dependencies: 1586
-- Name: COLUMN f_main.thema; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_main.thema IS 'Freifeld für Themenschlagworte. Worte durch Komma getrennt.';


--
-- TOC entry 1585 (class 1259 OID 17314)
-- Dependencies: 6 1586
-- Name: f_main_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE f_main_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 1986 (class 0 OID 0)
-- Dependencies: 1585
-- Name: f_main_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE f_main_id_seq OWNED BY f_main.id;


--
-- TOC entry 1587 (class 1259 OID 17369)
-- Dependencies: 1897 1898 1899 1900 1901 6 1586
-- Name: f_film; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_film (
    gattung integer,
    prodtechnik integer,
    fsk integer,
    praedikat integer,
    mediaspezi integer DEFAULT 0 NOT NULL,
    urauffuehr date,
    laenge interval
)
INHERITS (f_main);
ALTER TABLE ONLY f_film ALTER COLUMN prod_jahr SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_film ALTER COLUMN thema SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_film ALTER COLUMN quellen SET STORAGE EXTERNAL;


--
-- TOC entry 1988 (class 0 OID 0)
-- Dependencies: 1587
-- Name: TABLE f_film; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_film IS 'Tabelle der filmografischen Daten';


--
-- TOC entry 1989 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN f_film.gattung; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_film.gattung IS '=> f_gatt.gattung => s_string';


--
-- TOC entry 1990 (class 0 OID 0)
-- Dependencies: 1587
-- Name: COLUMN f_film.mediaspezi; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_film.mediaspezi IS 'Bitmaske
0 = s/w-Film
1 = Stummfilm
2 = ohne Sprache';


--
-- TOC entry 1588 (class 1259 OID 17390)
-- Dependencies: 6
-- Name: f_gatt; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_gatt (
    gattung integer NOT NULL
);


--
-- TOC entry 1589 (class 1259 OID 17405)
-- Dependencies: 6
-- Name: f_praed; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_praed (
    praed integer NOT NULL
);


--
-- TOC entry 1993 (class 0 OID 0)
-- Dependencies: 1589
-- Name: TABLE f_praed; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_praed IS '=> s__string (Verweis auf Prädikate)';


--
-- TOC entry 1566 (class 1259 OID 16619)
-- Dependencies: 6
-- Name: f_stitel; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_stitel (
    sertitel_id integer NOT NULL,
    titel character varying NOT NULL,
    descr text
);
ALTER TABLE ONLY f_stitel ALTER COLUMN titel SET STORAGE EXTERNAL;


--
-- TOC entry 1995 (class 0 OID 0)
-- Dependencies: 1566
-- Name: TABLE f_stitel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_stitel IS 'Titel aller Serien';


--
-- TOC entry 1568 (class 1259 OID 16661)
-- Dependencies: 1566 6
-- Name: f_sertitel_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE f_sertitel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 1997 (class 0 OID 0)
-- Dependencies: 1568
-- Name: f_sertitel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE f_sertitel_id_seq OWNED BY f_stitel.sertitel_id;


--
-- TOC entry 1584 (class 1259 OID 17299)
-- Dependencies: 6
-- Name: f_taetig; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_taetig (
    taetig integer NOT NULL
);


--
-- TOC entry 1999 (class 0 OID 0)
-- Dependencies: 1584
-- Name: TABLE f_taetig; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_taetig IS 'Tätigkeiten beim Film
[nur manuell editierbar!]';


--
-- TOC entry 1565 (class 1259 OID 16613)
-- Dependencies: 1875 6
-- Name: f_titel; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE f_titel (
    titel character varying NOT NULL,
    atitel character varying,
    sid integer,
    sfolge integer,
    id integer NOT NULL,
    utitel character varying,
    editfrom integer NOT NULL,
    editdate timestamp with time zone DEFAULT now() NOT NULL
);
ALTER TABLE ONLY f_titel ALTER COLUMN titel SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_titel ALTER COLUMN atitel SET STORAGE EXTERNAL;
ALTER TABLE ONLY f_titel ALTER COLUMN utitel SET STORAGE EXTERNAL;


--
-- TOC entry 2001 (class 0 OID 0)
-- Dependencies: 1565
-- Name: TABLE f_titel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE f_titel IS 'Tabelle der existierenden Titel für Film- und Bibliografische Einträge';


--
-- TOC entry 2002 (class 0 OID 0)
-- Dependencies: 1565
-- Name: COLUMN f_titel.titel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_titel.titel IS 'Der Originale Titel';


--
-- TOC entry 2003 (class 0 OID 0)
-- Dependencies: 1565
-- Name: COLUMN f_titel.atitel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_titel.atitel IS 'Arbeitstitel';


--
-- TOC entry 2004 (class 0 OID 0)
-- Dependencies: 1565
-- Name: COLUMN f_titel.sid; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_titel.sid IS 'Verweis auf die ID in titel.titel_serie
(Serientitel)';


--
-- TOC entry 2005 (class 0 OID 0)
-- Dependencies: 1565
-- Name: COLUMN f_titel.sfolge; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_titel.sfolge IS 'Gibt bei Serientiteln die fortlaufende Nummer wieder
Gibt es keine Nummer so wird 0 ausgegeben (nicht NULL).';


--
-- TOC entry 2006 (class 0 OID 0)
-- Dependencies: 1565
-- Name: COLUMN f_titel.utitel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN f_titel.utitel IS 'Untertitel oder deutsche Übersetzung';


--
-- TOC entry 1567 (class 1259 OID 16659)
-- Dependencies: 6 1565
-- Name: f_titel_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE f_titel_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2008 (class 0 OID 0)
-- Dependencies: 1567
-- Name: f_titel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE f_titel_id_seq OWNED BY f_titel.id;


--
-- TOC entry 1583 (class 1259 OID 17267)
-- Dependencies: 6
-- Name: i_objekt; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE i_objekt (
    id integer NOT NULL,
    lagerort character varying NOT NULL,
    notiz character varying
);


--
-- TOC entry 2010 (class 0 OID 0)
-- Dependencies: 1583
-- Name: TABLE i_objekt; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE i_objekt IS 'Abstraktes Datenmodell aller Gegenstände';


--
-- TOC entry 1582 (class 1259 OID 17265)
-- Dependencies: 1583 6
-- Name: i_objekt_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE i_objekt_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2012 (class 0 OID 0)
-- Dependencies: 1582
-- Name: i_objekt_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE i_objekt_id_seq OWNED BY i_objekt.id;


--
-- TOC entry 1591 (class 1259 OID 17417)
-- Dependencies: 6
-- Name: m_bild; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE m_bild (
    id integer NOT NULL,
    img bytea
);


--
-- TOC entry 2014 (class 0 OID 0)
-- Dependencies: 1591
-- Name: TABLE m_bild; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE m_bild IS 'Enthält alle Bilddaten im Rohformat';


--
-- TOC entry 1590 (class 1259 OID 17415)
-- Dependencies: 1591 6
-- Name: m_bild_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE m_bild_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2016 (class 0 OID 0)
-- Dependencies: 1590
-- Name: m_bild_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE m_bild_id_seq OWNED BY m_bild.id;


--
-- TOC entry 1580 (class 1259 OID 17156)
-- Dependencies: 6
-- Name: s_land; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE s_land (
    id integer NOT NULL,
    land character varying,
    bland character varying
);


--
-- TOC entry 2018 (class 0 OID 0)
-- Dependencies: 1580
-- Name: TABLE s_land; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE s_land IS 'Land-Bundesland Kombinationen';


--
-- TOC entry 1575 (class 1259 OID 16825)
-- Dependencies: 1888 6
-- Name: s_orte; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE s_orte (
    id integer NOT NULL,
    ort character varying NOT NULL,
    land integer DEFAULT 1 NOT NULL
);


--
-- TOC entry 2020 (class 0 OID 0)
-- Dependencies: 1575
-- Name: TABLE s_orte; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE s_orte IS 'Liste aller Orte (Städte und Gemeinden) mit Landeskennung';


--
-- TOC entry 1571 (class 1259 OID 16765)
-- Dependencies: 6
-- Name: p_alias; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE p_alias (
    id integer NOT NULL,
    name character varying NOT NULL,
    notiz text
);


--
-- TOC entry 2022 (class 0 OID 0)
-- Dependencies: 1571
-- Name: TABLE p_alias; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE p_alias IS 'Elterntabelle der Personen';


--
-- TOC entry 2023 (class 0 OID 0)
-- Dependencies: 1571
-- Name: COLUMN p_alias.name; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_alias.name IS 'Familien- oder Firmenname';


--
-- TOC entry 1570 (class 1259 OID 16763)
-- Dependencies: 6 1571
-- Name: p_alias_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE p_alias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2025 (class 0 OID 0)
-- Dependencies: 1570
-- Name: p_alias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE p_alias_id_seq OWNED BY p_alias.id;


--
-- TOC entry 2026 (class 0 OID 0)
-- Dependencies: 1570
-- Name: SEQUENCE p_alias_id_seq; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON SEQUENCE p_alias_id_seq IS 'Zähler für Personen';


--
-- TOC entry 1572 (class 1259 OID 16775)
-- Dependencies: 1878 1879 1880 6 1571
-- Name: p_person; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE p_person (
    vname character varying,
    gtag date DEFAULT '1900-01-01'::date NOT NULL,
    ttag date,
    strasse character varying,
    mail character varying,
    biogr text,
    notiz text,
    bild integer,
    tort integer,
    gort integer,
    wort integer,
    plz character(5),
    tel character varying,
    aliases integer,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    editfrom integer NOT NULL
)
INHERITS (p_alias);


--
-- TOC entry 2028 (class 0 OID 0)
-- Dependencies: 1572
-- Name: TABLE p_person; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE p_person IS 'Enthält den Bestand an natürlichen und juristischen Personen die in irgendeiner Weise mit dem DIAF in Konjunktion stehen.';


--
-- TOC entry 2029 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.vname; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.vname IS 'Vorname(n)';


--
-- TOC entry 2030 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.gtag; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.gtag IS 'Geburtstag / Gründungstag';


--
-- TOC entry 2031 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.ttag; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.ttag IS 'Sterbedatum / Tag der Auflösung';


--
-- TOC entry 2032 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.strasse; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.strasse IS 'Straße + Hausnummer und evt. Adresszusätze';


--
-- TOC entry 2033 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.mail; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.mail IS 'eMail-Adresse';


--
-- TOC entry 2034 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.biogr; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.biogr IS 'Biografie der Person';


--
-- TOC entry 2035 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.bild; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.bild IS 'Index auf Bilddaten';


--
-- TOC entry 2036 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.tort; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.tort IS 'Sterbeort';


--
-- TOC entry 2037 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.gort; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.gort IS 'Geburtsort/Gründungs-';


--
-- TOC entry 2038 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.wort; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.wort IS 'Wohnort/Standort';


--
-- TOC entry 2039 (class 0 OID 0)
-- Dependencies: 1572
-- Name: COLUMN p_person.tel; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN p_person.tel IS 'Telefonnummer (weitere im Notizfeld vermerken)';


--
-- TOC entry 1573 (class 1259 OID 16796)
-- Dependencies: 1881 1883 1884 1885 1886 6
-- Name: s_auth; Type: TABLE; Schema: public; Owner: -; Tablespace:
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
    editfrom integer DEFAULT 1 NOT NULL
);


--
-- TOC entry 2041 (class 0 OID 0)
-- Dependencies: 1573
-- Name: TABLE s_auth; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE s_auth IS 'Benutzerzugänge mit ihren jeweiligen Berechtigungen';


--
-- TOC entry 2042 (class 0 OID 0)
-- Dependencies: 1573
-- Name: COLUMN s_auth.lang; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON COLUMN s_auth.lang IS 'Sprachauswahl';


--
-- TOC entry 1578 (class 1259 OID 16978)
-- Dependencies: 6 1573
-- Name: s_auth_uid_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_auth_uid_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2044 (class 0 OID 0)
-- Dependencies: 1578
-- Name: s_auth_uid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE s_auth_uid_seq OWNED BY s_auth.uid;


--
-- TOC entry 2045 (class 0 OID 0)
-- Dependencies: 1578
-- Name: SEQUENCE s_auth_uid_seq; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON SEQUENCE s_auth_uid_seq IS 'Counter for Accounts';


--
-- TOC entry 1579 (class 1259 OID 17154)
-- Dependencies: 6 1580
-- Name: s_land_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_land_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2047 (class 0 OID 0)
-- Dependencies: 1579
-- Name: s_land_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE s_land_id_seq OWNED BY s_land.id;


--
-- TOC entry 1577 (class 1259 OID 16920)
-- Dependencies: 1890 6
-- Name: s_news; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE s_news (
    id integer NOT NULL,
    titel character varying(80) NOT NULL,
    inhalt text,
    editdate date DEFAULT now(),
    editfrom integer
);


--
-- TOC entry 2049 (class 0 OID 0)
-- Dependencies: 1577
-- Name: TABLE s_news; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE s_news IS 'Tabelle für das interne Board';


--
-- TOC entry 1576 (class 1259 OID 16918)
-- Dependencies: 1577 6
-- Name: s_news_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_news_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2051 (class 0 OID 0)
-- Dependencies: 1576
-- Name: s_news_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE s_news_id_seq OWNED BY s_news.id;


--
-- TOC entry 1574 (class 1259 OID 16823)
-- Dependencies: 6 1575
-- Name: s_orte_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE s_orte_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- TOC entry 2053 (class 0 OID 0)
-- Dependencies: 1574
-- Name: s_orte_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE s_orte_id_seq OWNED BY s_orte.id;


--
-- TOC entry 2054 (class 0 OID 0)
-- Dependencies: 1574
-- Name: SEQUENCE s_orte_id_seq; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON SEQUENCE s_orte_id_seq IS 'Orte  Id-counter';


--
-- TOC entry 1569 (class 1259 OID 16724)
-- Dependencies: 6
-- Name: s_strings; Type: TABLE; Schema: public; Owner: -; Tablespace:
--

CREATE TABLE s_strings (
    id integer NOT NULL,
    de character varying NOT NULL,
    en character varying
);


--
-- TOC entry 2056 (class 0 OID 0)
-- Dependencies: 1569
-- Name: TABLE s_strings; Type: COMMENT; Schema: public; Owner: -
--

COMMENT ON TABLE s_strings IS '
Enthält die Textpassagen für die Benutzeroberfläche (mehrsprachig)
Status-/Fehlermldg    1 -   499
Labels              500 - 9.999
Texte/Tooltips   10.000 -';


--
-- TOC entry 1893 (class 2604 OID 17319)
-- Dependencies: 1585 1586 1586
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE f_main ALTER COLUMN id SET DEFAULT nextval('f_main_id_seq'::regclass);


--
-- TOC entry 1876 (class 2604 OID 16709)
-- Dependencies: 1568 1566
-- Name: sertitel_id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE f_stitel ALTER COLUMN sertitel_id SET DEFAULT nextval('f_sertitel_id_seq'::regclass);


--
-- TOC entry 1874 (class 2604 OID 16708)
-- Dependencies: 1567 1565
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE f_titel ALTER COLUMN id SET DEFAULT nextval('f_titel_id_seq'::regclass);


--
-- TOC entry 1892 (class 2604 OID 17270)
-- Dependencies: 1583 1582 1583
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE i_objekt ALTER COLUMN id SET DEFAULT nextval('i_objekt_id_seq'::regclass);


--
-- TOC entry 1902 (class 2604 OID 17420)
-- Dependencies: 1590 1591 1591
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE m_bild ALTER COLUMN id SET DEFAULT nextval('m_bild_id_seq'::regclass);


--
-- TOC entry 1877 (class 2604 OID 16768)
-- Dependencies: 1570 1571 1571
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE p_alias ALTER COLUMN id SET DEFAULT nextval('p_alias_id_seq'::regclass);


--
-- TOC entry 1882 (class 2604 OID 16980)
-- Dependencies: 1578 1573
-- Name: uid; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE s_auth ALTER COLUMN uid SET DEFAULT nextval('s_auth_uid_seq'::regclass);


--
-- TOC entry 1891 (class 2604 OID 17159)
-- Dependencies: 1579 1580 1580
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE s_land ALTER COLUMN id SET DEFAULT nextval('s_land_id_seq'::regclass);


--
-- TOC entry 1889 (class 2604 OID 16923)
-- Dependencies: 1577 1576 1577
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE s_news ALTER COLUMN id SET DEFAULT nextval('s_news_id_seq'::regclass);


--
-- TOC entry 1887 (class 2604 OID 16828)
-- Dependencies: 1574 1575 1575
-- Name: id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE s_orte ALTER COLUMN id SET DEFAULT nextval('s_orte_id_seq'::regclass);


--
-- TOC entry 1939 (class 2606 OID 17234)
-- Dependencies: 1581 1581 1581 1581
-- Name: f_cast_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pkey PRIMARY KEY (fid, pid, tid);


--
-- TOC entry 1947 (class 2606 OID 17381)
-- Dependencies: 1587 1587
-- Name: f_film_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_pkey PRIMARY KEY (id);


--
-- TOC entry 1949 (class 2606 OID 17394)
-- Dependencies: 1588 1588
-- Name: f_gattung_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_gatt
    ADD CONSTRAINT f_gattung_pkey PRIMARY KEY (gattung);


--
-- TOC entry 1945 (class 2606 OID 17322)
-- Dependencies: 1586 1586
-- Name: f_main_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_pkey PRIMARY KEY (id);


--
-- TOC entry 1951 (class 2606 OID 17409)
-- Dependencies: 1589 1589
-- Name: f_praed_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_pkey PRIMARY KEY (praed);


--
-- TOC entry 1906 (class 2606 OID 17504)
-- Dependencies: 1566 1566
-- Name: f_stitel_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_pkey PRIMARY KEY (sertitel_id);


--
-- TOC entry 1908 (class 2606 OID 17506)
-- Dependencies: 1566 1566
-- Name: f_stitel_titel_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_titel_key UNIQUE (titel);


--
-- TOC entry 1943 (class 2606 OID 17308)
-- Dependencies: 1584 1584
-- Name: f_taetig_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_pkey PRIMARY KEY (taetig);


--
-- TOC entry 1941 (class 2606 OID 17275)
-- Dependencies: 1583 1583
-- Name: idx; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY i_objekt
    ADD CONSTRAINT idx PRIMARY KEY (id);


--
-- TOC entry 1953 (class 2606 OID 17425)
-- Dependencies: 1591 1591
-- Name: m_bild_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY m_bild
    ADD CONSTRAINT m_bild_pkey PRIMARY KEY (id);


--
-- TOC entry 1929 (class 2606 OID 16833)
-- Dependencies: 1575 1575
-- Name: ort_idx; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT ort_idx PRIMARY KEY (id);


--
-- TOC entry 1915 (class 2606 OID 16774)
-- Dependencies: 1571 1571
-- Name: p_alias_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY p_alias
    ADD CONSTRAINT p_alias_pkey PRIMARY KEY (id);


--
-- TOC entry 1921 (class 2606 OID 17178)
-- Dependencies: 1572 1572 1572
-- Name: p_person_name_gtag_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_name_gtag_key UNIQUE (name, gtag);


--
-- TOC entry 1923 (class 2606 OID 16784)
-- Dependencies: 1572 1572
-- Name: p_person_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_pkey PRIMARY KEY (id);


--
-- TOC entry 1925 (class 2606 OID 16988)
-- Dependencies: 1573 1573
-- Name: s_auth_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_pkey PRIMARY KEY (uid);


--
-- TOC entry 1927 (class 2606 OID 16990)
-- Dependencies: 1573 1573
-- Name: s_auth_username_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_username_key UNIQUE (username);


--
-- TOC entry 1935 (class 2606 OID 17166)
-- Dependencies: 1580 1580 1580
-- Name: s_land_land_bland_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_land_bland_key UNIQUE (land, bland);


--
-- TOC entry 1937 (class 2606 OID 17164)
-- Dependencies: 1580 1580
-- Name: s_land_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_pkey PRIMARY KEY (id);


--
-- TOC entry 1933 (class 2606 OID 16929)
-- Dependencies: 1577 1577
-- Name: s_news_pkey; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_pkey PRIMARY KEY (id);


--
-- TOC entry 1931 (class 2606 OID 16973)
-- Dependencies: 1575 1575 1575
-- Name: s_orte_ort_land_key; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT s_orte_ort_land_key UNIQUE (ort, land);


--
-- TOC entry 1910 (class 2606 OID 16731)
-- Dependencies: 1569 1569
-- Name: str_id; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT str_id PRIMARY KEY (id);


--
-- TOC entry 1904 (class 2606 OID 16680)
-- Dependencies: 1565 1565
-- Name: titel_idx; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY f_titel
    ADD CONSTRAINT titel_idx PRIMARY KEY (id);


--
-- TOC entry 1912 (class 2606 OID 16736)
-- Dependencies: 1569 1569
-- Name: uniq_de_text; Type: CONSTRAINT; Schema: public; Owner: -; Tablespace:
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT uniq_de_text UNIQUE (de);


--
-- TOC entry 1916 (class 1259 OID 16856)
-- Dependencies: 1572
-- Name: fki_gort_fidx; Type: INDEX; Schema: public; Owner: -; Tablespace:
--

CREATE INDEX fki_gort_fidx ON p_person USING btree (gort);


--
-- TOC entry 1917 (class 1259 OID 16850)
-- Dependencies: 1572
-- Name: fki_tort_fidx; Type: INDEX; Schema: public; Owner: -; Tablespace:
--

CREATE INDEX fki_tort_fidx ON p_person USING btree (tort);


--
-- TOC entry 1918 (class 1259 OID 17026)
-- Dependencies: 1572
-- Name: fki_uid_fidx; Type: INDEX; Schema: public; Owner: -; Tablespace:
--

CREATE INDEX fki_uid_fidx ON p_person USING btree (editfrom);


--
-- TOC entry 1919 (class 1259 OID 16862)
-- Dependencies: 1572
-- Name: fki_wort_fidx; Type: INDEX; Schema: public; Owner: -; Tablespace:
--

CREATE INDEX fki_wort_fidx ON p_person USING btree (wort);


--
-- TOC entry 1913 (class 1259 OID 16863)
-- Dependencies: 1571
-- Name: name_idx; Type: INDEX; Schema: public; Owner: -; Tablespace:
--

CREATE INDEX name_idx ON p_alias USING btree (name);


--
-- TOC entry 1963 (class 2606 OID 17364)
-- Dependencies: 1586 1581 1944
-- Name: f_cast_fid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_fid_fkey FOREIGN KEY (fid) REFERENCES f_main(id);


--
-- TOC entry 1961 (class 2606 OID 17281)
-- Dependencies: 1922 1572 1581
-- Name: f_cast_pid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pid_fkey FOREIGN KEY (pid) REFERENCES p_person(id) MATCH FULL;


--
-- TOC entry 1962 (class 2606 OID 17309)
-- Dependencies: 1584 1942 1581
-- Name: f_cast_tid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_tid_fkey FOREIGN KEY (tid) REFERENCES f_taetig(taetig);


--
-- TOC entry 1968 (class 2606 OID 17400)
-- Dependencies: 1588 1948 1587
-- Name: f_film_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_gattung_fkey FOREIGN KEY (gattung) REFERENCES f_gatt(gattung) MATCH FULL;


--
-- TOC entry 1969 (class 2606 OID 17522)
-- Dependencies: 1587 1950 1589
-- Name: f_film_praedikat_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_praedikat_fkey FOREIGN KEY (praedikat) REFERENCES f_praed(praed);


--
-- TOC entry 1970 (class 2606 OID 17395)
-- Dependencies: 1909 1588 1569
-- Name: f_gattung_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_gatt
    ADD CONSTRAINT f_gattung_gattung_fkey FOREIGN KEY (gattung) REFERENCES s_strings(id) MATCH FULL;


--
-- TOC entry 1967 (class 2606 OID 17517)
-- Dependencies: 1591 1952 1586
-- Name: f_main_bild_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_bild_id_fkey FOREIGN KEY (bild_id) REFERENCES m_bild(id);


--
-- TOC entry 1965 (class 2606 OID 17328)
-- Dependencies: 1573 1924 1586
-- Name: f_main_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid) MATCH FULL;


--
-- TOC entry 1966 (class 2606 OID 17341)
-- Dependencies: 1565 1586 1903
-- Name: f_main_titel_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_titel_id_fkey FOREIGN KEY (titel_id) REFERENCES f_titel(id) MATCH FULL;


--
-- TOC entry 1971 (class 2606 OID 17410)
-- Dependencies: 1589 1569 1909
-- Name: f_praed_praed_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_praed_fkey FOREIGN KEY (praed) REFERENCES s_strings(id);


--
-- TOC entry 1964 (class 2606 OID 17302)
-- Dependencies: 1569 1909 1584
-- Name: f_taetig_taetig_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_taetig_fkey FOREIGN KEY (taetig) REFERENCES s_strings(id);


--
-- TOC entry 1954 (class 2606 OID 17512)
-- Dependencies: 1565 1905 1566
-- Name: f_titel_sid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY f_titel
    ADD CONSTRAINT f_titel_sid_fkey FOREIGN KEY (sid) REFERENCES f_stitel(sertitel_id);


--
-- TOC entry 1956 (class 2606 OID 16851)
-- Dependencies: 1928 1575 1572
-- Name: gort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT gort_fidx FOREIGN KEY (gort) REFERENCES s_orte(id);


--
-- TOC entry 1959 (class 2606 OID 17081)
-- Dependencies: 1573 1924 1573
-- Name: s_auth_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 1960 (class 2606 OID 17107)
-- Dependencies: 1577 1924 1573
-- Name: s_news_autor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_autor_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 1955 (class 2606 OID 16845)
-- Dependencies: 1928 1572 1575
-- Name: tort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT tort_fidx FOREIGN KEY (tort) REFERENCES s_orte(id);


--
-- TOC entry 1958 (class 2606 OID 17021)
-- Dependencies: 1572 1573 1924
-- Name: uid_fidx; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT uid_fidx FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 1957 (class 2606 OID 16857)
-- Dependencies: 1572 1575 1928
-- Name: wort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT wort_fidx FOREIGN KEY (wort) REFERENCES s_orte(id);


--
-- TOC entry 1978 (class 0 OID 0)
-- Dependencies: 1581
-- Name: f_cast; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_cast FROM PUBLIC;
REVOKE ALL ON TABLE f_cast FROM diafadmin;
GRANT ALL ON TABLE f_cast TO diafadmin;
GRANT SELECT ON TABLE f_cast TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_cast TO diafuser;


--
-- TOC entry 1985 (class 0 OID 0)
-- Dependencies: 1586
-- Name: f_main; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_main FROM PUBLIC;
REVOKE ALL ON TABLE f_main FROM diafadmin;
GRANT ALL ON TABLE f_main TO diafadmin;
GRANT SELECT ON TABLE f_main TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_main TO diafuser;


--
-- TOC entry 1987 (class 0 OID 0)
-- Dependencies: 1585
-- Name: f_main_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE f_main_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE f_main_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE f_main_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE f_main_id_seq TO diafuser;


--
-- TOC entry 1991 (class 0 OID 0)
-- Dependencies: 1587
-- Name: f_film; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_film FROM PUBLIC;
REVOKE ALL ON TABLE f_film FROM diafadmin;
GRANT ALL ON TABLE f_film TO diafadmin;
GRANT SELECT ON TABLE f_film TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_film TO diafuser;


--
-- TOC entry 1992 (class 0 OID 0)
-- Dependencies: 1588
-- Name: f_gatt; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_gatt FROM PUBLIC;
REVOKE ALL ON TABLE f_gatt FROM diafadmin;
GRANT ALL ON TABLE f_gatt TO diafadmin;
GRANT SELECT ON TABLE f_gatt TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_gatt TO diafuser;


--
-- TOC entry 1994 (class 0 OID 0)
-- Dependencies: 1589
-- Name: f_praed; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_praed FROM PUBLIC;
REVOKE ALL ON TABLE f_praed FROM diafadmin;
GRANT ALL ON TABLE f_praed TO diafadmin;
GRANT SELECT ON TABLE f_praed TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_praed TO diafuser;


--
-- TOC entry 1996 (class 0 OID 0)
-- Dependencies: 1566
-- Name: f_stitel; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_stitel FROM PUBLIC;
REVOKE ALL ON TABLE f_stitel FROM diafadmin;
GRANT ALL ON TABLE f_stitel TO diafadmin;
GRANT SELECT ON TABLE f_stitel TO PUBLIC;


--
-- TOC entry 1998 (class 0 OID 0)
-- Dependencies: 1568
-- Name: f_sertitel_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE f_sertitel_id_seq TO diafadmin;
GRANT SELECT ON SEQUENCE f_sertitel_id_seq TO PUBLIC;


--
-- TOC entry 2000 (class 0 OID 0)
-- Dependencies: 1584
-- Name: f_taetig; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_taetig FROM PUBLIC;
REVOKE ALL ON TABLE f_taetig FROM diafadmin;
GRANT ALL ON TABLE f_taetig TO diafadmin;
GRANT SELECT ON TABLE f_taetig TO PUBLIC;
GRANT SELECT ON TABLE f_taetig TO diafuser;


--
-- TOC entry 2007 (class 0 OID 0)
-- Dependencies: 1565
-- Name: f_titel; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE f_titel FROM PUBLIC;
REVOKE ALL ON TABLE f_titel FROM diafadmin;
GRANT ALL ON TABLE f_titel TO diafadmin;
GRANT SELECT ON TABLE f_titel TO PUBLIC;


--
-- TOC entry 2009 (class 0 OID 0)
-- Dependencies: 1567
-- Name: f_titel_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE f_titel_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE f_titel_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE f_titel_id_seq TO diafadmin;
GRANT SELECT ON SEQUENCE f_titel_id_seq TO PUBLIC;


--
-- TOC entry 2011 (class 0 OID 0)
-- Dependencies: 1583
-- Name: i_objekt; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE i_objekt FROM PUBLIC;
REVOKE ALL ON TABLE i_objekt FROM diafadmin;
GRANT ALL ON TABLE i_objekt TO diafadmin;
GRANT SELECT ON TABLE i_objekt TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_objekt TO diafuser;


--
-- TOC entry 2013 (class 0 OID 0)
-- Dependencies: 1582
-- Name: i_objekt_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE i_objekt_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE i_objekt_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE i_objekt_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE i_objekt_id_seq TO diafuser;


--
-- TOC entry 2015 (class 0 OID 0)
-- Dependencies: 1591
-- Name: m_bild; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE m_bild FROM PUBLIC;
REVOKE ALL ON TABLE m_bild FROM diafadmin;
GRANT ALL ON TABLE m_bild TO diafadmin;
GRANT SELECT ON TABLE m_bild TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE m_bild TO diafuser;


--
-- TOC entry 2017 (class 0 OID 0)
-- Dependencies: 1590
-- Name: m_bild_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE m_bild_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE m_bild_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafuser;


--
-- TOC entry 2019 (class 0 OID 0)
-- Dependencies: 1580
-- Name: s_land; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE s_land FROM PUBLIC;
REVOKE ALL ON TABLE s_land FROM diafadmin;
GRANT ALL ON TABLE s_land TO diafadmin;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_land TO diafuser;
GRANT SELECT ON TABLE s_land TO PUBLIC;


--
-- TOC entry 2021 (class 0 OID 0)
-- Dependencies: 1575
-- Name: s_orte; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE s_orte FROM PUBLIC;
REVOKE ALL ON TABLE s_orte FROM diafadmin;
GRANT ALL ON TABLE s_orte TO diafadmin;
GRANT SELECT ON TABLE s_orte TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_orte TO diafuser;


--
-- TOC entry 2024 (class 0 OID 0)
-- Dependencies: 1571
-- Name: p_alias; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE p_alias FROM PUBLIC;
REVOKE ALL ON TABLE p_alias FROM diafadmin;
GRANT ALL ON TABLE p_alias TO diafadmin;
GRANT SELECT ON TABLE p_alias TO PUBLIC;


--
-- TOC entry 2027 (class 0 OID 0)
-- Dependencies: 1570
-- Name: p_alias_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE p_alias_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE p_alias_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE p_alias_id_seq TO diafadmin;
GRANT SELECT ON SEQUENCE p_alias_id_seq TO PUBLIC;


--
-- TOC entry 2040 (class 0 OID 0)
-- Dependencies: 1572
-- Name: p_person; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE p_person FROM PUBLIC;
REVOKE ALL ON TABLE p_person FROM diafadmin;
GRANT ALL ON TABLE p_person TO diafadmin;
GRANT SELECT ON TABLE p_person TO PUBLIC;


--
-- TOC entry 2043 (class 0 OID 0)
-- Dependencies: 1573
-- Name: s_auth; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE s_auth FROM PUBLIC;
REVOKE ALL ON TABLE s_auth FROM diafadmin;
GRANT ALL ON TABLE s_auth TO diafadmin;
GRANT SELECT ON TABLE s_auth TO PUBLIC;


--
-- TOC entry 2046 (class 0 OID 0)
-- Dependencies: 1578
-- Name: s_auth_uid_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafuser;


--
-- TOC entry 2048 (class 0 OID 0)
-- Dependencies: 1579
-- Name: s_land_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE s_land_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_land_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_land_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_land_id_seq TO diafuser;


--
-- TOC entry 2050 (class 0 OID 0)
-- Dependencies: 1577
-- Name: s_news; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE s_news FROM PUBLIC;
REVOKE ALL ON TABLE s_news FROM diafadmin;
GRANT ALL ON TABLE s_news TO diafadmin;
GRANT SELECT ON TABLE s_news TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_news TO diafuser;


--
-- TOC entry 2052 (class 0 OID 0)
-- Dependencies: 1576
-- Name: s_news_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE s_news_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_news_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_news_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_news_id_seq TO diafuser;


--
-- TOC entry 2055 (class 0 OID 0)
-- Dependencies: 1574
-- Name: s_orte_id_seq; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON SEQUENCE s_orte_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_orte_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_orte_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_orte_id_seq TO diafuser;


--
-- TOC entry 2057 (class 0 OID 0)
-- Dependencies: 1569
-- Name: s_strings; Type: ACL; Schema: public; Owner: -
--

REVOKE ALL ON TABLE s_strings FROM PUBLIC;
REVOKE ALL ON TABLE s_strings FROM diafadmin;
GRANT ALL ON TABLE s_strings TO diafadmin;
GRANT SELECT ON TABLE s_strings TO PUBLIC;


-- Completed on 2012-08-30 14:11:08 CEST

--
-- PostgreSQL database dump complete
--

