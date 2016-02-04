-- Table: fibers.node_type

-- DROP TABLE fibers.node_type;

CREATE TABLE fibers.node_type
(
  id serial NOT NULL,
  name character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT node_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.node_type
  OWNER TO opengeo;
