-- Table: fibers.region

-- DROP TABLE fibers.region;

CREATE TABLE fibers.region
(
  id serial NOT NULL,
  name character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT region_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.region
  OWNER TO opengeo;
