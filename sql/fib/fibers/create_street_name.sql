-- Table: fibers.street_name

-- DROP TABLE fibers.street_name;

CREATE TABLE fibers.street_name
(
  id integer NOT NULL DEFAULT nextval('fibers.street_name_id_seq'::regclass),
  name character varying(255),
  small_name character varying(255),
  area_id integer,
  descrip character varying(255),
  user_id integer,
  street_id integer,
  mag_city_id integer,
  mag_street_id integer,
  CONSTRAINT street_name_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.street_name
  OWNER TO opengeo;
