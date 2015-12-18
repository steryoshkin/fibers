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

-- Index: fibers.area_id

-- DROP INDEX fibers.area_id;

CREATE INDEX area_id
  ON fibers.area
  USING btree
  (id);

-- Index: fibers.area_name

-- DROP INDEX fibers.area_name;

CREATE INDEX area_name
  ON fibers.area
  USING btree
  (name COLLATE pg_catalog."default");

