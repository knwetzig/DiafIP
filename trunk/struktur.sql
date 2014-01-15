--
-- PostgreSQL database dump
--

-- Dumped from database version 9.1.4
-- Dumped by pg_dump version 9.1.4
-- Started on 2014-01-15 14:10:02 CET

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 2160 (class 1262 OID 25258)
-- Name: diafip; Type: DATABASE; Schema: -; Owner: diafadmin
--

CREATE DATABASE diafip WITH TEMPLATE = template0 ENCODING = 'UTF8' LC_COLLATE = 'C' LC_CTYPE = 'de_DE.UTF-8';


ALTER DATABASE diafip OWNER TO diafadmin;

\connect diafip

SET statement_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

--
-- TOC entry 2161 (class 1262 OID 25258)
-- Dependencies: 2160
-- Name: diafip; Type: COMMENT; Schema: -; Owner: diafadmin
--

COMMENT ON DATABASE diafip IS 'Datenbank für die Testumgebung von DIAFIP';


--
-- TOC entry 196 (class 3079 OID 11638)
-- Name: plpgsql; Type: EXTENSION; Schema: -; Owner: 
--

CREATE EXTENSION IF NOT EXISTS plpgsql WITH SCHEMA pg_catalog;


--
-- TOC entry 2164 (class 0 OID 0)
-- Dependencies: 196
-- Name: EXTENSION plpgsql; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION plpgsql IS 'PL/pgSQL procedural language';


SET search_path = public, pg_catalog;

SET default_tablespace = '';

SET default_with_oids = false;

--
-- TOC entry 161 (class 1259 OID 25259)
-- Dependencies: 6
-- Name: f_bformat; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_bformat (
    format character varying NOT NULL,
    id smallint NOT NULL
);


ALTER TABLE public.f_bformat OWNER TO diafadmin;

--
-- TOC entry 2165 (class 0 OID 0)
-- Dependencies: 161
-- Name: TABLE f_bformat; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_bformat IS 'Tabelle der Filmformate';


--
-- TOC entry 162 (class 1259 OID 25265)
-- Dependencies: 1994 1995 1996 6
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
-- TOC entry 2167 (class 0 OID 0)
-- Dependencies: 162
-- Name: TABLE f_main; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_main IS 'Die Stammtabelle für Filmografische und Bibliotheksdaten f_film/f_bibl
[abstract]';


--
-- TOC entry 2168 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN f_main.del; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.del IS 'zum löschen markiert';


--
-- TOC entry 2169 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN f_main.isvalid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.isvalid IS 'Datensätze mit "false" bedürfen einer Bearbeitung. Das trifft per Voreinstellung auf jeden neu angelegten Datensatz zu.';


--
-- TOC entry 2170 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN f_main.bild_id; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.bild_id IS '=> bildtabelle';


--
-- TOC entry 2171 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN f_main.thema; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.thema IS 'Freifeld für Themenschlagworte. Worte durch Komma getrennt.';


--
-- TOC entry 2172 (class 0 OID 0)
-- Dependencies: 162
-- Name: COLUMN f_main.anmerk; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_main.anmerk IS 'Ergänzende Angaben zum Objekt (sichtbar)';


--
-- TOC entry 163 (class 1259 OID 25274)
-- Dependencies: 6 162
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
-- TOC entry 2174 (class 0 OID 0)
-- Dependencies: 163
-- Name: TABLE f_biblio; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_biblio IS 'Tabelle der bibliografischen Daten
[erbt f_main]';


--
-- TOC entry 164 (class 1259 OID 25283)
-- Dependencies: 6
-- Name: f_cast; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_cast (
    fid integer NOT NULL,
    pid integer NOT NULL,
    tid integer NOT NULL
);


ALTER TABLE public.f_cast OWNER TO diafadmin;

--
-- TOC entry 2176 (class 0 OID 0)
-- Dependencies: 164
-- Name: TABLE f_cast; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_cast IS 'Tabelle der Besetzung in Filmen
Regisseure, Putzfrau etc....

Bei Büchern eben dann Authoren, Verleger etc..';


--
-- TOC entry 2177 (class 0 OID 0)
-- Dependencies: 164
-- Name: COLUMN f_cast.fid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.fid IS 'f_film => id';


--
-- TOC entry 2178 (class 0 OID 0)
-- Dependencies: 164
-- Name: COLUMN f_cast.pid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.pid IS 'p_person => id';


--
-- TOC entry 2179 (class 0 OID 0)
-- Dependencies: 164
-- Name: COLUMN f_cast.tid; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_cast.tid IS 'f_taetig => taetig';


--
-- TOC entry 165 (class 1259 OID 25286)
-- Dependencies: 162 6
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
-- TOC entry 2181 (class 0 OID 0)
-- Dependencies: 165
-- Name: TABLE f_film; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_film IS 'Tabelle der filmografischen Daten';


--
-- TOC entry 2182 (class 0 OID 0)
-- Dependencies: 165
-- Name: COLUMN f_film.gattung; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.gattung IS '=> f_gatt.gattung => s_string';


--
-- TOC entry 2183 (class 0 OID 0)
-- Dependencies: 165
-- Name: COLUMN f_film.fsk; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.fsk IS 'Altersempfehlung';


--
-- TOC entry 2184 (class 0 OID 0)
-- Dependencies: 165
-- Name: COLUMN f_film.mediaspezi; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_film.mediaspezi IS 'Bitmaske
0 = s/w-Film
1 = Stummfilm
2 = ohne Sprache';


--
-- TOC entry 166 (class 1259 OID 25295)
-- Dependencies: 6
-- Name: f_genre; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_genre (
    gattung smallint NOT NULL
);


ALTER TABLE public.f_genre OWNER TO diafadmin;

--
-- TOC entry 2186 (class 0 OID 0)
-- Dependencies: 166
-- Name: TABLE f_genre; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_genre IS 'Verweis auf Filmgattungen';


--
-- TOC entry 167 (class 1259 OID 25298)
-- Dependencies: 6
-- Name: f_mediaspezi; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_mediaspezi (
    mediaspezi smallint NOT NULL
);


ALTER TABLE public.f_mediaspezi OWNER TO diafadmin;

--
-- TOC entry 2188 (class 0 OID 0)
-- Dependencies: 167
-- Name: TABLE f_mediaspezi; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_mediaspezi IS 'Media-Spezifikationen';


--
-- TOC entry 168 (class 1259 OID 25301)
-- Dependencies: 6
-- Name: f_praed; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_praed (
    praed smallint NOT NULL
);


ALTER TABLE public.f_praed OWNER TO diafadmin;

--
-- TOC entry 2190 (class 0 OID 0)
-- Dependencies: 168
-- Name: TABLE f_praed; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_praed IS '=> s__string (Verweis auf Prädikate)';


--
-- TOC entry 169 (class 1259 OID 25304)
-- Dependencies: 6
-- Name: f_prodtechnik; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_prodtechnik (
    beschreibung smallint NOT NULL
);


ALTER TABLE public.f_prodtechnik OWNER TO diafadmin;

--
-- TOC entry 2192 (class 0 OID 0)
-- Dependencies: 169
-- Name: TABLE f_prodtechnik; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_prodtechnik IS 'Liste der eingesetzten Produktionstechniken beim Film';


--
-- TOC entry 2193 (class 0 OID 0)
-- Dependencies: 169
-- Name: COLUMN f_prodtechnik.beschreibung; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN f_prodtechnik.beschreibung IS 'Verweis auf Stringtabelle';


--
-- TOC entry 170 (class 1259 OID 25307)
-- Dependencies: 6
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
-- TOC entry 2195 (class 0 OID 0)
-- Dependencies: 170
-- Name: TABLE f_stitel; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_stitel IS 'Titel aller Serien';


--
-- TOC entry 171 (class 1259 OID 25313)
-- Dependencies: 170 6
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
-- TOC entry 2197 (class 0 OID 0)
-- Dependencies: 171
-- Name: f_sertitel_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE f_sertitel_id_seq OWNED BY f_stitel.sertitel_id;


--
-- TOC entry 172 (class 1259 OID 25315)
-- Dependencies: 6
-- Name: f_taetig; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE f_taetig (
    taetig smallint NOT NULL
);


ALTER TABLE public.f_taetig OWNER TO diafadmin;

--
-- TOC entry 2199 (class 0 OID 0)
-- Dependencies: 172
-- Name: TABLE f_taetig; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE f_taetig IS 'Tätigkeiten beim Film
[nur manuell editierbar!]';


--
-- TOC entry 173 (class 1259 OID 25318)
-- Dependencies: 162 6
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
-- TOC entry 2201 (class 0 OID 0)
-- Dependencies: 173
-- Name: id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE id_seq OWNED BY f_main.id;


--
-- TOC entry 2202 (class 0 OID 0)
-- Dependencies: 173
-- Name: SEQUENCE id_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE id_seq IS 'Zähler für p_alias (Personen), i_main (Gegenstände) und f_main (Filme)';


--
-- TOC entry 174 (class 1259 OID 25320)
-- Dependencies: 2007 2008 2009 2010 2011 2012 2013 6
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
-- TOC entry 2204 (class 0 OID 0)
-- Dependencies: 174
-- Name: TABLE i_main; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_main IS 'Abstraktes Datenmodell aller Gegenstände';


--
-- TOC entry 2205 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.kollo; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.kollo IS 'Objekt besteht aus "kollo" Stücken';


--
-- TOC entry 2206 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.akt_ort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.akt_ort IS 'aktueller Aufenthaltsort des Gegenstandes.
Wenn leer ist der Gegenstand am Lagerort.';


--
-- TOC entry 2207 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.a_wert; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.a_wert IS 'Index * Konst * Zeit = Versicherungswert';


--
-- TOC entry 2208 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.oldsig; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.oldsig IS 'Die alte Signatur á la AP-106
(feld kann nur bei Erstanlage bearbeitet werden)
Sperrfeld';


--
-- TOC entry 2209 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.in_date; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.in_date IS 'Zugangsdatum
(Alternativ Stunde null)';


--
-- TOC entry 2210 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.descr; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.descr IS 'Beschreibung des Gegenstandes';


--
-- TOC entry 2211 (class 0 OID 0)
-- Dependencies: 174
-- Name: COLUMN i_main.rest_report; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_main.rest_report IS 'Restaurierungsbericht';


--
-- TOC entry 175 (class 1259 OID 25333)
-- Dependencies: 6 174
-- Name: i_3dobj; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_3dobj (
    art integer,
    z smallint
)
INHERITS (i_main);


ALTER TABLE public.i_3dobj OWNER TO diafadmin;

--
-- TOC entry 2213 (class 0 OID 0)
-- Dependencies: 175
-- Name: TABLE i_3dobj; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_3dobj IS 'Alle räumlichen Gegenstände wie Puppen, Requisiten usw.';


--
-- TOC entry 176 (class 1259 OID 25346)
-- Dependencies: 6
-- Name: i_3dobj_art; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_3dobj_art (
    id smallint NOT NULL
);


ALTER TABLE public.i_3dobj_art OWNER TO diafadmin;

--
-- TOC entry 2215 (class 0 OID 0)
-- Dependencies: 176
-- Name: COLUMN i_3dobj_art.id; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_3dobj_art.id IS 'Handpuppen, Animationspuppen usw...';


--
-- TOC entry 177 (class 1259 OID 25349)
-- Dependencies: 2021 2029 6 174
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
-- TOC entry 2217 (class 0 OID 0)
-- Dependencies: 177
-- Name: TABLE i_fkop; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_fkop IS 'Tabelle der Filmmedien';


--
-- TOC entry 2218 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN i_fkop.medium; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.medium IS 'Verweis auf Tabelle -> i_medium';


--
-- TOC entry 2219 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN i_fkop.material; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.material IS 'Verweis auf i_material';


--
-- TOC entry 2220 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN i_fkop.tonart; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.tonart IS 'Verweis auf i_tonart';


--
-- TOC entry 2221 (class 0 OID 0)
-- Dependencies: 177
-- Name: COLUMN i_fkop.fps; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_fkop.fps IS 'frames/s';


--
-- TOC entry 178 (class 1259 OID 25363)
-- Dependencies: 2030 6
-- Name: s_orte; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE s_orte (
    id integer NOT NULL,
    ort character varying NOT NULL,
    land integer DEFAULT 1 NOT NULL
);


ALTER TABLE public.s_orte OWNER TO diafadmin;

--
-- TOC entry 2223 (class 0 OID 0)
-- Dependencies: 178
-- Name: TABLE s_orte; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_orte IS 'Liste aller Orte (Städte und Gemeinden) mit Landeskennung';


--
-- TOC entry 179 (class 1259 OID 25370)
-- Dependencies: 6 178
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
-- TOC entry 2225 (class 0 OID 0)
-- Dependencies: 179
-- Name: service_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE service_id_seq OWNED BY s_orte.id;


--
-- TOC entry 2226 (class 0 OID 0)
-- Dependencies: 179
-- Name: SEQUENCE service_id_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE service_id_seq IS 'Orte  Id-counter /
Landcounter';


--
-- TOC entry 180 (class 1259 OID 25372)
-- Dependencies: 2032 6
-- Name: i_lagerort; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_lagerort (
    nr integer DEFAULT nextval('service_id_seq'::regclass) NOT NULL,
    lagerort character varying NOT NULL
);


ALTER TABLE public.i_lagerort OWNER TO diafadmin;

--
-- TOC entry 2228 (class 0 OID 0)
-- Dependencies: 180
-- Name: TABLE i_lagerort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_lagerort IS 'Liste der möglichen Lagerorte von Gegenständen';


--
-- TOC entry 181 (class 1259 OID 25379)
-- Dependencies: 6
-- Name: i_material; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_material (
    id smallint NOT NULL,
    material character varying NOT NULL
);


ALTER TABLE public.i_material OWNER TO diafadmin;

--
-- TOC entry 2230 (class 0 OID 0)
-- Dependencies: 181
-- Name: TABLE i_material; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_material IS 'wird im Moment nur von FKop genutzt';


--
-- TOC entry 182 (class 1259 OID 25385)
-- Dependencies: 6
-- Name: i_medium; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_medium (
    id smallint NOT NULL,
    medium character varying NOT NULL
);


ALTER TABLE public.i_medium OWNER TO diafadmin;

--
-- TOC entry 183 (class 1259 OID 25391)
-- Dependencies: 174 6
-- Name: i_planar; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_planar (
    art integer
)
INHERITS (i_main);


ALTER TABLE public.i_planar OWNER TO diafadmin;

--
-- TOC entry 2233 (class 0 OID 0)
-- Dependencies: 183
-- Name: TABLE i_planar; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE i_planar IS 'Alle flachen Gegenstände wie Plakate, Dokumente und Fotos';


--
-- TOC entry 184 (class 1259 OID 25404)
-- Dependencies: 6
-- Name: i_planar_art; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_planar_art (
    id smallint NOT NULL
);


ALTER TABLE public.i_planar_art OWNER TO diafadmin;

--
-- TOC entry 185 (class 1259 OID 25407)
-- Dependencies: 6
-- Name: i_tonart; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE i_tonart (
    id smallint NOT NULL,
    audiotyp character varying NOT NULL
);


ALTER TABLE public.i_tonart OWNER TO diafadmin;

--
-- TOC entry 2236 (class 0 OID 0)
-- Dependencies: 185
-- Name: COLUMN i_tonart.audiotyp; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN i_tonart.audiotyp IS 'Lichtton, Dolby-D';


--
-- TOC entry 186 (class 1259 OID 25413)
-- Dependencies: 6
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
-- TOC entry 187 (class 1259 OID 25419)
-- Dependencies: 186 6
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
-- TOC entry 2239 (class 0 OID 0)
-- Dependencies: 187
-- Name: m_bild_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE m_bild_id_seq OWNED BY m_bild.id;


--
-- TOC entry 188 (class 1259 OID 25421)
-- Dependencies: 2041 6
-- Name: s_land; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE s_land (
    id integer DEFAULT nextval('service_id_seq'::regclass) NOT NULL,
    land character varying,
    bland character varying
);


ALTER TABLE public.s_land OWNER TO diafadmin;

--
-- TOC entry 2241 (class 0 OID 0)
-- Dependencies: 188
-- Name: TABLE s_land; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_land IS 'Land-Bundesland Kombinationen
Diese Kombination bezeichnet ausdrücklich den geografischen und nicht den politischen Sachverhalt. Demnach liegt Karl-Marx-Stadt genauso wie Chemnitz in "Deutschland,Sachsen".';


--
-- TOC entry 189 (class 1259 OID 25428)
-- Dependencies: 1993 6
-- Name: orte; Type: VIEW; Schema: public; Owner: diafadmin
--

CREATE VIEW orte AS
    SELECT s_orte.ort, s_land.land, s_land.bland, s_orte.id AS oid, s_land.id AS lid FROM s_orte, s_land WHERE (s_orte.land = s_land.id) ORDER BY s_orte.ort, s_land.land;


ALTER TABLE public.orte OWNER TO diafadmin;

--
-- TOC entry 190 (class 1259 OID 25432)
-- Dependencies: 2042 6
-- Name: p_alias; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE TABLE p_alias (
    id integer DEFAULT nextval('id_seq'::regclass) NOT NULL,
    name character varying NOT NULL,
    notiz text
);


ALTER TABLE public.p_alias OWNER TO diafadmin;

--
-- TOC entry 2244 (class 0 OID 0)
-- Dependencies: 190
-- Name: TABLE p_alias; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE p_alias IS 'Elterntabelle der Personen';


--
-- TOC entry 2245 (class 0 OID 0)
-- Dependencies: 190
-- Name: COLUMN p_alias.name; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_alias.name IS 'Familien- oder Firmenname';


--
-- TOC entry 191 (class 1259 OID 25439)
-- Dependencies: 2043 2044 2045 2046 6 190
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
    plz character(5),
    tel character varying,
    aliases integer,
    editdate timestamp with time zone DEFAULT now() NOT NULL,
    editfrom smallint NOT NULL,
    del boolean DEFAULT false
)
INHERITS (p_alias);


ALTER TABLE public.p_person OWNER TO diafadmin;

--
-- TOC entry 2247 (class 0 OID 0)
-- Dependencies: 191
-- Name: TABLE p_person; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE p_person IS 'Enthält den Bestand an natürlichen und juristischen Personen die in irgendeiner Weise mit dem DIAF in Konjunktion stehen.';


--
-- TOC entry 2248 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.vname; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.vname IS 'Vorname(n)';


--
-- TOC entry 2249 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.gtag; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.gtag IS 'Geburtstag / Gründungstag';


--
-- TOC entry 2250 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.ttag; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.ttag IS 'Sterbedatum / Tag der Auflösung';


--
-- TOC entry 2251 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.strasse; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.strasse IS 'Straße + Hausnummer und evt. Adresszusätze';


--
-- TOC entry 2252 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.mail; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.mail IS 'eMail-Adresse';


--
-- TOC entry 2253 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.biogr; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.biogr IS 'Biografie der Person';


--
-- TOC entry 2254 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.bild; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.bild IS 'Index auf Bilddaten';


--
-- TOC entry 2255 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.tort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.tort IS 'Sterbeort';


--
-- TOC entry 2256 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.gort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.gort IS 'Geburtsort/Gründungs-';


--
-- TOC entry 2257 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.wort; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.wort IS 'Wohnort/Standort';


--
-- TOC entry 2258 (class 0 OID 0)
-- Dependencies: 191
-- Name: COLUMN p_person.tel; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN p_person.tel IS 'Telefonnummer (weitere im Notizfeld vermerken)';


--
-- TOC entry 192 (class 1259 OID 25450)
-- Dependencies: 2048 2049 2050 2051 2052 6
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
-- TOC entry 2260 (class 0 OID 0)
-- Dependencies: 192
-- Name: TABLE s_auth; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_auth IS 'Benutzerzugänge mit ihren jeweiligen Berechtigungen';


--
-- TOC entry 2261 (class 0 OID 0)
-- Dependencies: 192
-- Name: COLUMN s_auth.lang; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON COLUMN s_auth.lang IS 'Sprachauswahl';


--
-- TOC entry 193 (class 1259 OID 25461)
-- Dependencies: 192 6
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
-- TOC entry 2263 (class 0 OID 0)
-- Dependencies: 193
-- Name: s_auth_uid_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: diafadmin
--

ALTER SEQUENCE s_auth_uid_seq OWNED BY s_auth.uid;


--
-- TOC entry 2264 (class 0 OID 0)
-- Dependencies: 193
-- Name: SEQUENCE s_auth_uid_seq; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON SEQUENCE s_auth_uid_seq IS 'Counter for Accounts';


--
-- TOC entry 194 (class 1259 OID 25463)
-- Dependencies: 2054 2055 6
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
-- TOC entry 2266 (class 0 OID 0)
-- Dependencies: 194
-- Name: TABLE s_news; Type: COMMENT; Schema: public; Owner: diafadmin
--

COMMENT ON TABLE s_news IS 'Tabelle für das interne Board';


--
-- TOC entry 195 (class 1259 OID 25471)
-- Dependencies: 6
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
-- TOC entry 2268 (class 0 OID 0)
-- Dependencies: 195
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
-- TOC entry 1998 (class 2604 OID 25477)
-- Dependencies: 163 163 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 1999 (class 2604 OID 25478)
-- Dependencies: 163 163
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN del SET DEFAULT false;


--
-- TOC entry 2000 (class 2604 OID 25479)
-- Dependencies: 163 163
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN editdate SET DEFAULT now();


--
-- TOC entry 2001 (class 2604 OID 25480)
-- Dependencies: 163 163
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio ALTER COLUMN isvalid SET DEFAULT false;


--
-- TOC entry 2004 (class 2604 OID 25481)
-- Dependencies: 165 165 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2005 (class 2604 OID 25482)
-- Dependencies: 165 165
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN del SET DEFAULT false;


--
-- TOC entry 2002 (class 2604 OID 25483)
-- Dependencies: 165 165
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN editdate SET DEFAULT now();


--
-- TOC entry 2003 (class 2604 OID 25484)
-- Dependencies: 165 165
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film ALTER COLUMN isvalid SET DEFAULT false;


--
-- TOC entry 1997 (class 2604 OID 25485)
-- Dependencies: 173 162
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_main ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2006 (class 2604 OID 25486)
-- Dependencies: 171 170
-- Name: sertitel_id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_stitel ALTER COLUMN sertitel_id SET DEFAULT nextval('f_sertitel_id_seq'::regclass);


--
-- TOC entry 2014 (class 2604 OID 25487)
-- Dependencies: 175 175 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2015 (class 2604 OID 25488)
-- Dependencies: 175 175
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN leihbar SET DEFAULT false;


--
-- TOC entry 2016 (class 2604 OID 25489)
-- Dependencies: 175 175
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN kollo SET DEFAULT 1;


--
-- TOC entry 2017 (class 2604 OID 25490)
-- Dependencies: 175 175
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- TOC entry 2018 (class 2604 OID 25491)
-- Dependencies: 175 175
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN del SET DEFAULT false;


--
-- TOC entry 2019 (class 2604 OID 25492)
-- Dependencies: 175 175
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN editdate SET DEFAULT now();


--
-- TOC entry 2020 (class 2604 OID 25493)
-- Dependencies: 175 175
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj ALTER COLUMN isvalid SET DEFAULT false;


--
-- TOC entry 2022 (class 2604 OID 25494)
-- Dependencies: 177 173 177
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2023 (class 2604 OID 25495)
-- Dependencies: 177 177
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN leihbar SET DEFAULT false;


--
-- TOC entry 2024 (class 2604 OID 25496)
-- Dependencies: 177 177
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN kollo SET DEFAULT 1;


--
-- TOC entry 2025 (class 2604 OID 25497)
-- Dependencies: 177 177
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- TOC entry 2026 (class 2604 OID 25498)
-- Dependencies: 177 177
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN del SET DEFAULT false;


--
-- TOC entry 2027 (class 2604 OID 25499)
-- Dependencies: 177 177
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN editdate SET DEFAULT now();


--
-- TOC entry 2028 (class 2604 OID 25500)
-- Dependencies: 177 177
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop ALTER COLUMN isvalid SET DEFAULT false;


--
-- TOC entry 2033 (class 2604 OID 25501)
-- Dependencies: 183 173 183
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2034 (class 2604 OID 25502)
-- Dependencies: 183 183
-- Name: leihbar; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN leihbar SET DEFAULT false;


--
-- TOC entry 2035 (class 2604 OID 25503)
-- Dependencies: 183 183
-- Name: kollo; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN kollo SET DEFAULT 1;


--
-- TOC entry 2036 (class 2604 OID 25504)
-- Dependencies: 183 183
-- Name: in_date; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN in_date SET DEFAULT '1993-11-16'::date;


--
-- TOC entry 2037 (class 2604 OID 25505)
-- Dependencies: 183 183
-- Name: del; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN del SET DEFAULT false;


--
-- TOC entry 2038 (class 2604 OID 25506)
-- Dependencies: 183 183
-- Name: editdate; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN editdate SET DEFAULT now();


--
-- TOC entry 2039 (class 2604 OID 25507)
-- Dependencies: 183 183
-- Name: isvalid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar ALTER COLUMN isvalid SET DEFAULT false;


--
-- TOC entry 2040 (class 2604 OID 25508)
-- Dependencies: 187 186
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY m_bild ALTER COLUMN id SET DEFAULT nextval('m_bild_id_seq'::regclass);


--
-- TOC entry 2047 (class 2604 OID 25509)
-- Dependencies: 191 191 173
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person ALTER COLUMN id SET DEFAULT nextval('id_seq'::regclass);


--
-- TOC entry 2053 (class 2604 OID 25510)
-- Dependencies: 193 192
-- Name: uid; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_auth ALTER COLUMN uid SET DEFAULT nextval('s_auth_uid_seq'::regclass);


--
-- TOC entry 2031 (class 2604 OID 25511)
-- Dependencies: 179 178
-- Name: id; Type: DEFAULT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_orte ALTER COLUMN id SET DEFAULT nextval('service_id_seq'::regclass);


--
-- TOC entry 2057 (class 2606 OID 25513)
-- Dependencies: 161 161
-- Name: f_bformat_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_bformat
    ADD CONSTRAINT f_bformat_pkey PRIMARY KEY (id);


--
-- TOC entry 2061 (class 2606 OID 25515)
-- Dependencies: 163 163
-- Name: f_biblio_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_biblio
    ADD CONSTRAINT f_biblio_pkey PRIMARY KEY (id);


--
-- TOC entry 2063 (class 2606 OID 25517)
-- Dependencies: 164 164 164 164
-- Name: f_cast_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pkey PRIMARY KEY (fid, pid, tid);


--
-- TOC entry 2065 (class 2606 OID 25519)
-- Dependencies: 165 165
-- Name: f_film_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_pkey PRIMARY KEY (id);


--
-- TOC entry 2067 (class 2606 OID 25521)
-- Dependencies: 166 166
-- Name: f_gattung_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_genre
    ADD CONSTRAINT f_gattung_pkey PRIMARY KEY (gattung);


--
-- TOC entry 2059 (class 2606 OID 25523)
-- Dependencies: 162 162
-- Name: f_main_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_pkey PRIMARY KEY (id);


--
-- TOC entry 2071 (class 2606 OID 25525)
-- Dependencies: 168 168
-- Name: f_praed_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_pkey PRIMARY KEY (praed);


--
-- TOC entry 2073 (class 2606 OID 25527)
-- Dependencies: 169 169
-- Name: f_prodtechnik_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_prodtechnik
    ADD CONSTRAINT f_prodtechnik_pkey PRIMARY KEY (beschreibung);


--
-- TOC entry 2075 (class 2606 OID 25529)
-- Dependencies: 170 170
-- Name: f_stitel_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_pkey PRIMARY KEY (sertitel_id);


--
-- TOC entry 2077 (class 2606 OID 25531)
-- Dependencies: 170 170
-- Name: f_stitel_titel_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_stitel
    ADD CONSTRAINT f_stitel_titel_key UNIQUE (titel);


--
-- TOC entry 2079 (class 2606 OID 25533)
-- Dependencies: 172 172
-- Name: f_taetig_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_pkey PRIMARY KEY (taetig);


--
-- TOC entry 2085 (class 2606 OID 25535)
-- Dependencies: 176 176
-- Name: i_3dobj_art_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_3dobj_art
    ADD CONSTRAINT i_3dobj_art_pkey PRIMARY KEY (id);


--
-- TOC entry 2083 (class 2606 OID 25537)
-- Dependencies: 175 175
-- Name: i_3dobj_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_3dobj
    ADD CONSTRAINT i_3dobj_pkey PRIMARY KEY (id);


--
-- TOC entry 2087 (class 2606 OID 25539)
-- Dependencies: 177 177
-- Name: i_fkop_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_pkey PRIMARY KEY (id);


--
-- TOC entry 2095 (class 2606 OID 25541)
-- Dependencies: 181 181
-- Name: i_material_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_material
    ADD CONSTRAINT i_material_pkey PRIMARY KEY (id);


--
-- TOC entry 2097 (class 2606 OID 25543)
-- Dependencies: 182 182
-- Name: i_medium_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_medium
    ADD CONSTRAINT i_medium_pkey PRIMARY KEY (id);


--
-- TOC entry 2101 (class 2606 OID 25545)
-- Dependencies: 184 184
-- Name: i_planar_art_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_planar_art
    ADD CONSTRAINT i_planar_art_pkey PRIMARY KEY (id);


--
-- TOC entry 2099 (class 2606 OID 25547)
-- Dependencies: 183 183
-- Name: i_planar_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_planar
    ADD CONSTRAINT i_planar_pkey PRIMARY KEY (id);


--
-- TOC entry 2103 (class 2606 OID 25549)
-- Dependencies: 185 185
-- Name: i_tonart_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_tonart
    ADD CONSTRAINT i_tonart_pkey PRIMARY KEY (id);


--
-- TOC entry 2081 (class 2606 OID 25551)
-- Dependencies: 174 174
-- Name: idx; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT idx PRIMARY KEY (id);


--
-- TOC entry 2105 (class 2606 OID 25553)
-- Dependencies: 186 186
-- Name: images_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY m_bild
    ADD CONSTRAINT images_pkey PRIMARY KEY (id);


--
-- TOC entry 2093 (class 2606 OID 25555)
-- Dependencies: 180 180
-- Name: lagerort_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY i_lagerort
    ADD CONSTRAINT lagerort_pkey PRIMARY KEY (nr);


--
-- TOC entry 2069 (class 2606 OID 25557)
-- Dependencies: 167 167
-- Name: mediaspezi_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY f_mediaspezi
    ADD CONSTRAINT mediaspezi_pkey PRIMARY KEY (mediaspezi);


--
-- TOC entry 2089 (class 2606 OID 25559)
-- Dependencies: 178 178
-- Name: ort_idx; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT ort_idx PRIMARY KEY (id);


--
-- TOC entry 2112 (class 2606 OID 25561)
-- Dependencies: 190 190
-- Name: p_alias_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY p_alias
    ADD CONSTRAINT p_alias_pkey PRIMARY KEY (id);


--
-- TOC entry 2118 (class 2606 OID 25563)
-- Dependencies: 191 191
-- Name: p_person_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_pkey PRIMARY KEY (id);


--
-- TOC entry 2120 (class 2606 OID 25565)
-- Dependencies: 191 191 191 191
-- Name: p_person_unique; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT p_person_unique UNIQUE (vname, name, gtag);


--
-- TOC entry 2122 (class 2606 OID 25567)
-- Dependencies: 192 192
-- Name: s_auth_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_pkey PRIMARY KEY (uid);


--
-- TOC entry 2124 (class 2606 OID 25569)
-- Dependencies: 192 192
-- Name: s_auth_username_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_username_key UNIQUE (username);


--
-- TOC entry 2107 (class 2606 OID 25571)
-- Dependencies: 188 188 188
-- Name: s_land_land_bland_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_land_bland_key UNIQUE (land, bland);


--
-- TOC entry 2109 (class 2606 OID 25573)
-- Dependencies: 188 188
-- Name: s_land_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_land
    ADD CONSTRAINT s_land_pkey PRIMARY KEY (id);


--
-- TOC entry 2126 (class 2606 OID 25575)
-- Dependencies: 194 194
-- Name: s_news_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_pkey PRIMARY KEY (id);


--
-- TOC entry 2091 (class 2606 OID 25577)
-- Dependencies: 178 178 178
-- Name: s_orte_ort_land_key; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT s_orte_ort_land_key UNIQUE (ort, land);


--
-- TOC entry 2128 (class 2606 OID 25579)
-- Dependencies: 195 195
-- Name: str_id; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT str_id PRIMARY KEY (id);


--
-- TOC entry 2130 (class 2606 OID 25581)
-- Dependencies: 195 195
-- Name: uniq_de_text; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
--

ALTER TABLE ONLY s_strings
    ADD CONSTRAINT uniq_de_text UNIQUE (de);


--
-- TOC entry 2113 (class 1259 OID 25582)
-- Dependencies: 191
-- Name: fki_gort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE INDEX fki_gort_fidx ON p_person USING btree (gort);


--
-- TOC entry 2114 (class 1259 OID 25583)
-- Dependencies: 191
-- Name: fki_tort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE INDEX fki_tort_fidx ON p_person USING btree (tort);


--
-- TOC entry 2115 (class 1259 OID 25584)
-- Dependencies: 191
-- Name: fki_uid_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE INDEX fki_uid_fidx ON p_person USING btree (editfrom);


--
-- TOC entry 2116 (class 1259 OID 25585)
-- Dependencies: 191
-- Name: fki_wort_fidx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE INDEX fki_wort_fidx ON p_person USING btree (wort);


--
-- TOC entry 2110 (class 1259 OID 25586)
-- Dependencies: 190
-- Name: name_idx; Type: INDEX; Schema: public; Owner: diafadmin; Tablespace: 
--

CREATE INDEX name_idx ON p_alias USING btree (name);


--
-- TOC entry 2132 (class 2606 OID 25587)
-- Dependencies: 2121 163 192
-- Name: f_biblio_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_biblio
    ADD CONSTRAINT f_biblio_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid) MATCH FULL;


--
-- TOC entry 2133 (class 2606 OID 25592)
-- Dependencies: 2117 191 164
-- Name: f_cast_pid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_pid_fkey FOREIGN KEY (pid) REFERENCES p_person(id) MATCH FULL;


--
-- TOC entry 2134 (class 2606 OID 25597)
-- Dependencies: 2078 164 172
-- Name: f_cast_tid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_cast
    ADD CONSTRAINT f_cast_tid_fkey FOREIGN KEY (tid) REFERENCES f_taetig(taetig);


--
-- TOC entry 2135 (class 2606 OID 25602)
-- Dependencies: 2121 192 165
-- Name: f_film_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid) MATCH FULL;


--
-- TOC entry 2136 (class 2606 OID 25607)
-- Dependencies: 2066 166 165
-- Name: f_film_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_gattung_fkey FOREIGN KEY (gattung) REFERENCES f_genre(gattung) MATCH FULL;


--
-- TOC entry 2137 (class 2606 OID 25612)
-- Dependencies: 2070 165 168
-- Name: f_film_praedikat_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_film
    ADD CONSTRAINT f_film_praedikat_fkey FOREIGN KEY (praedikat) REFERENCES f_praed(praed);


--
-- TOC entry 2138 (class 2606 OID 25617)
-- Dependencies: 2127 195 166
-- Name: f_gattung_gattung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_genre
    ADD CONSTRAINT f_gattung_gattung_fkey FOREIGN KEY (gattung) REFERENCES s_strings(id) MATCH FULL;


--
-- TOC entry 2131 (class 2606 OID 25622)
-- Dependencies: 162 170 2074
-- Name: f_main_sid_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_main
    ADD CONSTRAINT f_main_sid_fkey FOREIGN KEY (sid) REFERENCES f_stitel(sertitel_id) MATCH FULL;


--
-- TOC entry 2139 (class 2606 OID 25627)
-- Dependencies: 167 195 2127
-- Name: f_mediaspezi_mediaspezi_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_mediaspezi
    ADD CONSTRAINT f_mediaspezi_mediaspezi_fkey FOREIGN KEY (mediaspezi) REFERENCES s_strings(id);


--
-- TOC entry 2140 (class 2606 OID 25632)
-- Dependencies: 2127 195 168
-- Name: f_praed_praed_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_praed
    ADD CONSTRAINT f_praed_praed_fkey FOREIGN KEY (praed) REFERENCES s_strings(id);


--
-- TOC entry 2141 (class 2606 OID 25637)
-- Dependencies: 169 195 2127
-- Name: f_prodtechnik_beschreibung_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_prodtechnik
    ADD CONSTRAINT f_prodtechnik_beschreibung_fkey FOREIGN KEY (beschreibung) REFERENCES s_strings(id) MATCH FULL;


--
-- TOC entry 2142 (class 2606 OID 25642)
-- Dependencies: 172 2127 195
-- Name: f_taetig_taetig_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY f_taetig
    ADD CONSTRAINT f_taetig_taetig_fkey FOREIGN KEY (taetig) REFERENCES s_strings(id);


--
-- TOC entry 2152 (class 2606 OID 25647)
-- Dependencies: 191 178 2088
-- Name: gort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT gort_fidx FOREIGN KEY (gort) REFERENCES s_orte(id);


--
-- TOC entry 2146 (class 2606 OID 25652)
-- Dependencies: 176 195 2127
-- Name: i_3dobj_art_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj_art
    ADD CONSTRAINT i_3dobj_art_id_fkey FOREIGN KEY (id) REFERENCES s_strings(id);


--
-- TOC entry 2145 (class 2606 OID 25657)
-- Dependencies: 176 2084 175
-- Name: i_3dobj_art_id_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_3dobj
    ADD CONSTRAINT i_3dobj_art_id_fkey FOREIGN KEY (art) REFERENCES i_3dobj_art(id);


--
-- TOC entry 2147 (class 2606 OID 25662)
-- Dependencies: 177 2094 181
-- Name: i_fkop_material_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_material_fkey FOREIGN KEY (material) REFERENCES i_material(id);


--
-- TOC entry 2148 (class 2606 OID 25667)
-- Dependencies: 177 2096 182
-- Name: i_fkop_medium_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_medium_fkey FOREIGN KEY (medium) REFERENCES i_medium(id);


--
-- TOC entry 2149 (class 2606 OID 25672)
-- Dependencies: 177 2102 185
-- Name: i_fkop_tonart_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_fkop
    ADD CONSTRAINT i_fkop_tonart_fkey FOREIGN KEY (tonart) REFERENCES i_tonart(id);


--
-- TOC entry 2143 (class 2606 OID 25677)
-- Dependencies: 174 2117 191
-- Name: i_objekt_eigner_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT i_objekt_eigner_fkey FOREIGN KEY (eigner) REFERENCES p_person(id);


--
-- TOC entry 2144 (class 2606 OID 25682)
-- Dependencies: 174 2117 191
-- Name: i_objekt_herkunft_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_main
    ADD CONSTRAINT i_objekt_herkunft_fkey FOREIGN KEY (herkunft) REFERENCES p_person(id);


--
-- TOC entry 2151 (class 2606 OID 25687)
-- Dependencies: 183 2100 184
-- Name: i_planar_art_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY i_planar
    ADD CONSTRAINT i_planar_art_fkey FOREIGN KEY (art) REFERENCES i_planar_art(id);


--
-- TOC entry 2156 (class 2606 OID 25692)
-- Dependencies: 2121 192 192
-- Name: s_auth_editfrom_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_auth
    ADD CONSTRAINT s_auth_editfrom_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 2157 (class 2606 OID 25697)
-- Dependencies: 192 2121 194
-- Name: s_news_autor_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_news
    ADD CONSTRAINT s_news_autor_fkey FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 2150 (class 2606 OID 25702)
-- Dependencies: 188 178 2108
-- Name: s_orte_land_fkey; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY s_orte
    ADD CONSTRAINT s_orte_land_fkey FOREIGN KEY (land) REFERENCES s_land(id);


--
-- TOC entry 2153 (class 2606 OID 25707)
-- Dependencies: 191 178 2088
-- Name: tort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT tort_fidx FOREIGN KEY (tort) REFERENCES s_orte(id);


--
-- TOC entry 2154 (class 2606 OID 25712)
-- Dependencies: 192 191 2121
-- Name: uid_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT uid_fidx FOREIGN KEY (editfrom) REFERENCES s_auth(uid);


--
-- TOC entry 2155 (class 2606 OID 25717)
-- Dependencies: 178 2088 191
-- Name: wort_fidx; Type: FK CONSTRAINT; Schema: public; Owner: diafadmin
--

ALTER TABLE ONLY p_person
    ADD CONSTRAINT wort_fidx FOREIGN KEY (wort) REFERENCES s_orte(id);


--
-- TOC entry 2163 (class 0 OID 0)
-- Dependencies: 6
-- Name: public; Type: ACL; Schema: -; Owner: postgres
--

REVOKE ALL ON SCHEMA public FROM PUBLIC;
REVOKE ALL ON SCHEMA public FROM postgres;
GRANT ALL ON SCHEMA public TO postgres;
GRANT ALL ON SCHEMA public TO diafadmin;
GRANT USAGE ON SCHEMA public TO PUBLIC;


--
-- TOC entry 2166 (class 0 OID 0)
-- Dependencies: 161
-- Name: f_bformat; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_bformat FROM PUBLIC;
REVOKE ALL ON TABLE f_bformat FROM diafadmin;
GRANT ALL ON TABLE f_bformat TO diafadmin;
GRANT SELECT ON TABLE f_bformat TO PUBLIC;


--
-- TOC entry 2173 (class 0 OID 0)
-- Dependencies: 162
-- Name: f_main; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_main FROM PUBLIC;
REVOKE ALL ON TABLE f_main FROM diafadmin;
GRANT ALL ON TABLE f_main TO diafadmin;
GRANT SELECT ON TABLE f_main TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_main TO diafuser;


--
-- TOC entry 2175 (class 0 OID 0)
-- Dependencies: 163
-- Name: f_biblio; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_biblio FROM PUBLIC;
REVOKE ALL ON TABLE f_biblio FROM diafadmin;
GRANT ALL ON TABLE f_biblio TO diafadmin;
GRANT SELECT ON TABLE f_biblio TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_biblio TO diafuser;


--
-- TOC entry 2180 (class 0 OID 0)
-- Dependencies: 164
-- Name: f_cast; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_cast FROM PUBLIC;
REVOKE ALL ON TABLE f_cast FROM diafadmin;
GRANT ALL ON TABLE f_cast TO diafadmin;
GRANT SELECT ON TABLE f_cast TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_cast TO diafuser;


--
-- TOC entry 2185 (class 0 OID 0)
-- Dependencies: 165
-- Name: f_film; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_film FROM PUBLIC;
REVOKE ALL ON TABLE f_film FROM diafadmin;
GRANT ALL ON TABLE f_film TO diafadmin;
GRANT SELECT ON TABLE f_film TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE f_film TO diafuser;


--
-- TOC entry 2187 (class 0 OID 0)
-- Dependencies: 166
-- Name: f_genre; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_genre FROM PUBLIC;
REVOKE ALL ON TABLE f_genre FROM diafadmin;
GRANT ALL ON TABLE f_genre TO diafadmin;
GRANT SELECT ON TABLE f_genre TO PUBLIC;


--
-- TOC entry 2189 (class 0 OID 0)
-- Dependencies: 167
-- Name: f_mediaspezi; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_mediaspezi FROM PUBLIC;
REVOKE ALL ON TABLE f_mediaspezi FROM diafadmin;
GRANT ALL ON TABLE f_mediaspezi TO diafadmin;
GRANT SELECT ON TABLE f_mediaspezi TO PUBLIC;


--
-- TOC entry 2191 (class 0 OID 0)
-- Dependencies: 168
-- Name: f_praed; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_praed FROM PUBLIC;
REVOKE ALL ON TABLE f_praed FROM diafadmin;
GRANT ALL ON TABLE f_praed TO diafadmin;
GRANT SELECT ON TABLE f_praed TO PUBLIC;


--
-- TOC entry 2194 (class 0 OID 0)
-- Dependencies: 169
-- Name: f_prodtechnik; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_prodtechnik FROM PUBLIC;
REVOKE ALL ON TABLE f_prodtechnik FROM diafadmin;
GRANT ALL ON TABLE f_prodtechnik TO diafadmin;
GRANT SELECT ON TABLE f_prodtechnik TO PUBLIC;


--
-- TOC entry 2196 (class 0 OID 0)
-- Dependencies: 170
-- Name: f_stitel; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_stitel FROM PUBLIC;
REVOKE ALL ON TABLE f_stitel FROM diafadmin;
GRANT ALL ON TABLE f_stitel TO diafadmin;
GRANT SELECT ON TABLE f_stitel TO PUBLIC;


--
-- TOC entry 2198 (class 0 OID 0)
-- Dependencies: 171
-- Name: f_sertitel_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE f_sertitel_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE f_sertitel_id_seq TO diafadmin;
GRANT SELECT ON SEQUENCE f_sertitel_id_seq TO PUBLIC;
GRANT ALL ON SEQUENCE f_sertitel_id_seq TO diafuser;


--
-- TOC entry 2200 (class 0 OID 0)
-- Dependencies: 172
-- Name: f_taetig; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE f_taetig FROM PUBLIC;
REVOKE ALL ON TABLE f_taetig FROM diafadmin;
GRANT ALL ON TABLE f_taetig TO diafadmin;
GRANT SELECT ON TABLE f_taetig TO PUBLIC;


--
-- TOC entry 2203 (class 0 OID 0)
-- Dependencies: 173
-- Name: id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE id_seq TO diafadmin;
GRANT ALL ON SEQUENCE id_seq TO diafuser;


--
-- TOC entry 2212 (class 0 OID 0)
-- Dependencies: 174
-- Name: i_main; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_main FROM PUBLIC;
REVOKE ALL ON TABLE i_main FROM diafadmin;
GRANT ALL ON TABLE i_main TO diafadmin;
GRANT SELECT ON TABLE i_main TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_main TO diafuser;


--
-- TOC entry 2214 (class 0 OID 0)
-- Dependencies: 175
-- Name: i_3dobj; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_3dobj FROM PUBLIC;
REVOKE ALL ON TABLE i_3dobj FROM diafadmin;
GRANT ALL ON TABLE i_3dobj TO diafadmin;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_3dobj TO diafuser;
GRANT SELECT ON TABLE i_3dobj TO PUBLIC;


--
-- TOC entry 2216 (class 0 OID 0)
-- Dependencies: 176
-- Name: i_3dobj_art; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_3dobj_art FROM PUBLIC;
REVOKE ALL ON TABLE i_3dobj_art FROM diafadmin;
GRANT ALL ON TABLE i_3dobj_art TO diafadmin;
GRANT SELECT ON TABLE i_3dobj_art TO PUBLIC;


--
-- TOC entry 2222 (class 0 OID 0)
-- Dependencies: 177
-- Name: i_fkop; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_fkop FROM PUBLIC;
REVOKE ALL ON TABLE i_fkop FROM diafadmin;
GRANT ALL ON TABLE i_fkop TO diafadmin;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_fkop TO diafuser;
GRANT SELECT ON TABLE i_fkop TO PUBLIC;


--
-- TOC entry 2224 (class 0 OID 0)
-- Dependencies: 178
-- Name: s_orte; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_orte FROM PUBLIC;
REVOKE ALL ON TABLE s_orte FROM diafadmin;
GRANT ALL ON TABLE s_orte TO diafadmin;
GRANT SELECT ON TABLE s_orte TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_orte TO diafuser;


--
-- TOC entry 2227 (class 0 OID 0)
-- Dependencies: 179
-- Name: service_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE service_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE service_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE service_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE service_id_seq TO diafuser;


--
-- TOC entry 2229 (class 0 OID 0)
-- Dependencies: 180
-- Name: i_lagerort; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_lagerort FROM PUBLIC;
REVOKE ALL ON TABLE i_lagerort FROM diafadmin;
GRANT ALL ON TABLE i_lagerort TO diafadmin;
GRANT SELECT ON TABLE i_lagerort TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_lagerort TO diafuser;


--
-- TOC entry 2231 (class 0 OID 0)
-- Dependencies: 181
-- Name: i_material; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_material FROM PUBLIC;
REVOKE ALL ON TABLE i_material FROM diafadmin;
GRANT ALL ON TABLE i_material TO diafadmin;
GRANT SELECT ON TABLE i_material TO PUBLIC;


--
-- TOC entry 2232 (class 0 OID 0)
-- Dependencies: 182
-- Name: i_medium; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_medium FROM PUBLIC;
REVOKE ALL ON TABLE i_medium FROM diafadmin;
GRANT ALL ON TABLE i_medium TO diafadmin;
GRANT SELECT ON TABLE i_medium TO PUBLIC;


--
-- TOC entry 2234 (class 0 OID 0)
-- Dependencies: 183
-- Name: i_planar; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_planar FROM PUBLIC;
REVOKE ALL ON TABLE i_planar FROM diafadmin;
GRANT ALL ON TABLE i_planar TO diafadmin;
GRANT SELECT ON TABLE i_planar TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE i_planar TO diafuser;


--
-- TOC entry 2235 (class 0 OID 0)
-- Dependencies: 184
-- Name: i_planar_art; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_planar_art FROM PUBLIC;
REVOKE ALL ON TABLE i_planar_art FROM diafadmin;
GRANT ALL ON TABLE i_planar_art TO diafadmin;
GRANT SELECT ON TABLE i_planar_art TO PUBLIC;


--
-- TOC entry 2237 (class 0 OID 0)
-- Dependencies: 185
-- Name: i_tonart; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE i_tonart FROM PUBLIC;
REVOKE ALL ON TABLE i_tonart FROM diafadmin;
GRANT ALL ON TABLE i_tonart TO diafadmin;
GRANT SELECT ON TABLE i_tonart TO PUBLIC;


--
-- TOC entry 2238 (class 0 OID 0)
-- Dependencies: 186
-- Name: m_bild; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE m_bild FROM PUBLIC;
REVOKE ALL ON TABLE m_bild FROM diafadmin;
GRANT ALL ON TABLE m_bild TO diafadmin;
GRANT SELECT ON TABLE m_bild TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE m_bild TO diafuser;


--
-- TOC entry 2240 (class 0 OID 0)
-- Dependencies: 187
-- Name: m_bild_id_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE m_bild_id_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE m_bild_id_seq FROM diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafadmin;
GRANT ALL ON SEQUENCE m_bild_id_seq TO diafuser;


--
-- TOC entry 2242 (class 0 OID 0)
-- Dependencies: 188
-- Name: s_land; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_land FROM PUBLIC;
REVOKE ALL ON TABLE s_land FROM diafadmin;
GRANT ALL ON TABLE s_land TO diafadmin;
GRANT SELECT ON TABLE s_land TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_land TO diafuser;


--
-- TOC entry 2243 (class 0 OID 0)
-- Dependencies: 189
-- Name: orte; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE orte FROM PUBLIC;
REVOKE ALL ON TABLE orte FROM diafadmin;
GRANT ALL ON TABLE orte TO diafadmin;
GRANT SELECT ON TABLE orte TO PUBLIC;


--
-- TOC entry 2246 (class 0 OID 0)
-- Dependencies: 190
-- Name: p_alias; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE p_alias FROM PUBLIC;
REVOKE ALL ON TABLE p_alias FROM diafadmin;
GRANT ALL ON TABLE p_alias TO diafadmin;
GRANT SELECT ON TABLE p_alias TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE p_alias TO diafuser;


--
-- TOC entry 2259 (class 0 OID 0)
-- Dependencies: 191
-- Name: p_person; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE p_person FROM PUBLIC;
REVOKE ALL ON TABLE p_person FROM diafadmin;
GRANT ALL ON TABLE p_person TO diafadmin;
GRANT SELECT ON TABLE p_person TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE p_person TO diafuser;


--
-- TOC entry 2262 (class 0 OID 0)
-- Dependencies: 192
-- Name: s_auth; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_auth FROM PUBLIC;
REVOKE ALL ON TABLE s_auth FROM diafadmin;
GRANT ALL ON TABLE s_auth TO diafadmin;
GRANT SELECT ON TABLE s_auth TO PUBLIC;


--
-- TOC entry 2265 (class 0 OID 0)
-- Dependencies: 193
-- Name: s_auth_uid_seq; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM PUBLIC;
REVOKE ALL ON SEQUENCE s_auth_uid_seq FROM diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafadmin;
GRANT ALL ON SEQUENCE s_auth_uid_seq TO diafuser;


--
-- TOC entry 2267 (class 0 OID 0)
-- Dependencies: 194
-- Name: s_news; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_news FROM PUBLIC;
REVOKE ALL ON TABLE s_news FROM diafadmin;
GRANT ALL ON TABLE s_news TO diafadmin;
GRANT SELECT ON TABLE s_news TO PUBLIC;
GRANT SELECT,INSERT,DELETE,UPDATE ON TABLE s_news TO diafuser;


--
-- TOC entry 2269 (class 0 OID 0)
-- Dependencies: 195
-- Name: s_strings; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL ON TABLE s_strings FROM PUBLIC;
REVOKE ALL ON TABLE s_strings FROM diafadmin;
GRANT ALL ON TABLE s_strings TO diafadmin;
GRANT SELECT ON TABLE s_strings TO PUBLIC;
GRANT SELECT,UPDATE ON TABLE s_strings TO diafuser;


--
-- TOC entry 2270 (class 0 OID 0)
-- Dependencies: 195
-- Name: s_strings.en; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL(en) ON TABLE s_strings FROM PUBLIC;
REVOKE ALL(en) ON TABLE s_strings FROM diafadmin;
GRANT UPDATE(en) ON TABLE s_strings TO diafuser;


--
-- TOC entry 2271 (class 0 OID 0)
-- Dependencies: 195
-- Name: s_strings.fr; Type: ACL; Schema: public; Owner: diafadmin
--

REVOKE ALL(fr) ON TABLE s_strings FROM PUBLIC;
REVOKE ALL(fr) ON TABLE s_strings FROM diafadmin;
GRANT UPDATE(fr) ON TABLE s_strings TO diafuser;


-- Completed on 2014-01-15 14:10:02 CET

--
-- PostgreSQL database dump complete
--

