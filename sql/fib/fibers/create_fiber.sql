-- Table: fibers.fiber

-- DROP TABLE fibers.fiber;

CREATE TABLE fibers.fiber
(
  id serial NOT NULL,
  cable_id integer,
  num integer,
  descrip character varying(255),
  user_id integer,
  mod_color integer,
  fib_color integer,
  CONSTRAINT fibers_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.fiber
  OWNER TO opengeo;
