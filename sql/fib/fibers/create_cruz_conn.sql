-- Table: fibers.cruz_conn

-- DROP TABLE fibers.cruz_conn;

CREATE TABLE fibers.cruz_conn
(
  id serial NOT NULL,
  pq_id integer,
  port integer,
  used boolean,
  fiber_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT cruz_conn_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.cruz_conn
  OWNER TO opengeo;
