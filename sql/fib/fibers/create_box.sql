-- Table: fibers.box

-- DROP TABLE fibers.box;

CREATE TABLE fibers.box
(
  id integer NOT NULL DEFAULT nextval('fibers.box_id_seq'::regclass),
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
