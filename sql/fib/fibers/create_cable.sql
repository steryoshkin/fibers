-- Table: fibers.cable

-- DROP TABLE fibers.cable;

CREATE TABLE fibers.cable
(
  id serial NOT NULL,
  pq_1 integer,
  pq_2 integer,
  cable_type integer,
  fib2 integer,
  descrip character varying(255),
  user_id integer,
  the_geom geometry(LineString,4326),
  CONSTRAINT cable_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.cable
  OWNER TO opengeo;
