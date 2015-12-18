-- Table: fibers.ups_type

-- DROP TABLE fibers.ups_type;

CREATE TABLE fibers.ups_type
(
  id integer NOT NULL DEFAULT nextval('fibers.ups_type_id_seq'::regclass),
  name character varying(255),
  unit integer,
  power numeric(10,2),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT ups_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.ups_type
  OWNER TO opengeo;
