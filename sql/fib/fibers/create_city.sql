-- Table: fibers.city

-- DROP TABLE fibers.city;

CREATE TABLE fibers.city
(
  id serial NOT NULL,
  name character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT city_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.city
  OWNER TO opengeo;

-- Index: fibers.city_id

-- DROP INDEX fibers.city_id;

CREATE INDEX city_id
  ON fibers.city
  USING btree
  (id);

-- Index: fibers.city_name

-- DROP INDEX fibers.city_name;

CREATE INDEX city_name
  ON fibers.city
  USING btree
  (name COLLATE pg_catalog."default");

