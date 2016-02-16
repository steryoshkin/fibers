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
