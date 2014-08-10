-- TABELLE ENTITY

-- Name: entity; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
CREATE TABLE entity (
    id integer NOT NULL,
    bereich character(1),
    descr character varying,
    bilder integer[],
    notiz character varying,
    isvalid boolean DEFAULT false,
    del boolean DEFAULT false,
    editfrom smallint,
    editdate bigint
);


ALTER TABLE public.entity OWNER TO diafadmin;

COMMENT ON TABLE entity IS 'Das ist die abstrakte Elterntabelle aller Objekte';
COMMENT ON COLUMN entity.id IS 'Enthält die IDs aller nachfolgenden Bereiche';
COMMENT ON COLUMN entity.bereich IS 'Enthält die Kennung zu welchem Bereich die Entität gehört.
Das vermeidet die aufwendige Suche in der DB';
COMMENT ON COLUMN entity.descr IS 'Beschreibung bzw. Biografie bei Personen';
COMMENT ON COLUMN entity.notiz IS 'selbsterklärend ;-)';
COMMENT ON COLUMN entity.isvalid IS 'Flag zur Kennzeichnung, das dieser Datensatz abschließend bearbeitet wurde';
COMMENT ON COLUMN entity.del IS 'Löschflag';
COMMENT ON COLUMN entity.editfrom IS 'uid des Bearbeiters';

-- Name: entity_id_seq; Type: SEQUENCE; Schema: public; Owner: diafadmin
CREATE SEQUENCE entity_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;

ALTER TABLE public.entity_id_seq OWNER TO diafadmin;
ALTER SEQUENCE entity_id_seq OWNED BY entity.id;
SELECT pg_catalog.setval('entity_id_seq', 1001, true);

ALTER TABLE ONLY entity ALTER COLUMN id SET DEFAULT nextval('entity_id_seq'::regclass);
ALTER TABLE ONLY entity
    ADD CONSTRAINT entity_pkey PRIMARY KEY (id);

REVOKE ALL ON TABLE entity FROM PUBLIC;
REVOKE ALL ON TABLE entity FROM diafadmin;
GRANT ALL ON TABLE entity TO diafadmin;
GRANT SELECT,INSERT,UPDATE ON TABLE entity TO diafuser;




-- TABELLE NAMEN

-- Name: p_namen; Type: TABLE; Schema: public; Owner: diafadmin; Tablespace: 
CREATE TABLE p_namen (
    vname character varying DEFAULT '-'::character varying NOT NULL,
    name character varying NOT NULL
)
INHERITS (entity);

ALTER TABLE public.p_namen OWNER TO diafadmin;

COMMENT ON COLUMN p_namen.vname IS 'Vorname(n)';
COMMENT ON COLUMN p_namen.name IS 'Familien- oder Firmenname';

ALTER TABLE ONLY p_namen ALTER COLUMN id SET DEFAULT nextval('entity_id_seq'::regclass);
ALTER TABLE ONLY p_namen ALTER COLUMN isvalid SET DEFAULT false;
ALTER TABLE ONLY p_namen ALTER COLUMN del SET DEFAULT false;

-- Name: p_namen_pkey; Type: CONSTRAINT; Schema: public; Owner: diafadmin; Tablespace: 
ALTER TABLE ONLY p_namen
    ADD CONSTRAINT p_namen_pkey PRIMARY KEY (id);

-- Name: p_namen; Type: ACL; Schema: public; Owner: diafadmin
REVOKE ALL ON TABLE p_namen FROM PUBLIC;
REVOKE ALL ON TABLE p_namen FROM diafadmin;
GRANT ALL ON TABLE p_namen TO diafadmin;
GRANT SELECT,INSERT,UPDATE ON TABLE p_namen TO diafuser;
