-- Table: fibers.cable_type

-- DROP TABLE fibers.cable_type;

CREATE TABLE fibers.cable_type
(
  id integer NOT NULL DEFAULT nextval('fibers.cable_type_id_seq'::regclass),
  name character varying(255),
  fib integer,
  descrip character varying(255),
  user_id integer,
  type integer NOT NULL DEFAULT 0,
  CONSTRAINT cable_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.cable_type
  OWNER TO opengeo;
