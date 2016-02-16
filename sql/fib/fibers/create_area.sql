-- Table: fibers.area

-- DROP TABLE fibers.area;

CREATE TABLE fibers.area
(
  id serial NOT NULL,
  name character varying(255),
  descrip character varying(255),
  user_id integer,
  city_id integer,
  region_id integer,
  CONSTRAINT area_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.area
  OWNER TO opengeo;
