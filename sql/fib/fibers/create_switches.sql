-- Table: fibers.switches

-- DROP TABLE fibers.switches;

CREATE TABLE fibers.switches
(
  id integer NOT NULL DEFAULT nextval('fibers.switches_id_seq'::regclass),
  node_id integer,
  switch_type_id integer,
  used_ports integer,
  descrip character varying(255),
  user_id integer,
  ip inet,
  CONSTRAINT switches_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.switches
  OWNER TO opengeo;
