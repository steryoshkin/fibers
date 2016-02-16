-- Table: fibers.mc_type

-- DROP TABLE fibers.mc_type;

CREATE TABLE fibers.mc_type
(
  id serial NOT NULL,
  name character varying(255),
  power numeric(10,2),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT mc_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.mc_type
  OWNER TO opengeo;
