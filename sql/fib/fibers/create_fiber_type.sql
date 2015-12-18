-- Table: fibers.fiber_type

-- DROP TABLE fibers.fiber_type;

CREATE TABLE fibers.fiber_type
(
  id serial NOT NULL,
  cable_id integer,
  num integer,
  descrip character varying(255),
  user_id integer,
  mod_color integer,
  fib_color integer,
  CONSTRAINT fiber_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.fiber_type
  OWNER TO opengeo;
