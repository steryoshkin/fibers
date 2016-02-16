-- Table: fibers.lift_type

-- DROP TABLE fibers.lift_type;

CREATE TABLE fibers.lift_type
(
  id serial NOT NULL,
  name character varying(255),
  tel character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT lift_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.lift_type
  OWNER TO opengeo;
