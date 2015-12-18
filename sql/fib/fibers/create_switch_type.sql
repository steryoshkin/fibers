-- Table: fibers.switch_type

-- DROP TABLE fibers.switch_type;

CREATE TABLE fibers.switch_type
(
  id integer NOT NULL DEFAULT nextval('fibers.switch_type_id_seq'::regclass),
  name character varying(255),
  ports_num integer,
  unit integer,
  power numeric(10,2),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT switch_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.switch_type
  OWNER TO opengeo;
