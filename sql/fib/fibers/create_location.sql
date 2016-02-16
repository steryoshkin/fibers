-- Table: fibers.location

-- DROP TABLE fibers.location;

CREATE TABLE fibers.location
(
  id serial NOT NULL,
  location character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT location_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.location
  OWNER TO opengeo;
