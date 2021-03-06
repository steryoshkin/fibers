-- Table: fibers.box_type

-- DROP TABLE fibers.box_type;

CREATE TABLE fibers.box_type
(
  id serial NOT NULL,
  name character varying(255),
  unit integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT box_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.box_type
  OWNER TO opengeo;
