-- Table: fibers.keys

-- DROP TABLE fibers.keys;

CREATE TABLE fibers.keys
(
  id serial NOT NULL,
  num character varying(255),
  node_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT keys_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.keys
  OWNER TO opengeo;
