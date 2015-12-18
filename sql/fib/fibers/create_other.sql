-- Table: fibers.other

-- DROP TABLE fibers.other;

CREATE TABLE fibers.other
(
  id serial NOT NULL,
  node_id integer,
  other_type_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT other_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.other
  OWNER TO opengeo;
