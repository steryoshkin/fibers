-- Table: fibers.switches

-- DROP TABLE fibers.switches;

CREATE TABLE fibers.switches
(
  id serial NOT NULL,
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
