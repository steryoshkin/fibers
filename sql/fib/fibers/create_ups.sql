-- Table: fibers.ups

-- DROP TABLE fibers.ups;

CREATE TABLE fibers.ups
(
  id serial NOT NULL,
  node_id integer,
  ups_type_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT ups_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.ups
  OWNER TO opengeo;
