-- Table: fibers.box

-- DROP TABLE fibers.box;

CREATE TABLE fibers.box
(
  id serial NOT NULL,
  node_id integer,
  box_type_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT box_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.box
  OWNER TO opengeo;
