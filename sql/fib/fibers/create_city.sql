-- Table: fibers.city

-- DROP TABLE fibers.city;

CREATE TABLE fibers.city
(
  id serial NOT NULL,
  name character varying(255),
  descrip character varying(255),
  region_id integer,
  user_id integer,
  the_geom geometry(Point,4326),
  CONSTRAINT city_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.city
  OWNER TO opengeo;
