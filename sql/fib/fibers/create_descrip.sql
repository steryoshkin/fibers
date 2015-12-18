-- Table: fibers.descrip

-- DROP TABLE fibers.descrip;

CREATE TABLE fibers.descrip
(
  id integer NOT NULL DEFAULT nextval('fibers.descrip_id_seq'::regclass),
  text text,
  node_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT desc_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.descrip
  OWNER TO opengeo;
