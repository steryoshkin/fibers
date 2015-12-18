-- Table: fibers.fiber_conn

-- DROP TABLE fibers.fiber_conn;

CREATE TABLE fibers.fiber_conn
(
  id serial NOT NULL,
  fiber_id_1 integer,
  fiber_id_2 integer,
  node_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT fiber_conn_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.fiber_conn
  OWNER TO opengeo;
