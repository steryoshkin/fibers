-- Table: fibers.other_type

-- DROP TABLE fibers.other_type;

CREATE TABLE fibers.other_type
(
  id serial NOT NULL,
  name character varying(255),
  unit integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT other_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.other_type
  OWNER TO opengeo;
