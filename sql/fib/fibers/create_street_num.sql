-- Table: fibers.street_num

-- DROP TABLE fibers.street_num;

CREATE TABLE fibers.street_num
(
  id serial NOT NULL,
  street_name_id integer,
  num character varying(255),
  descrip character varying(255),
  user_id integer,
  mag_street_num integer,
  CONSTRAINT street_num_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.street_num
  OWNER TO opengeo;
