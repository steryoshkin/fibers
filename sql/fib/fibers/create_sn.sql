-- Table: fibers.sn

-- DROP TABLE fibers.sn;

CREATE TABLE fibers.sn
(
  id serial NOT NULL,
  sn character varying(50),
  eq integer,
  eq_type character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT sn_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.sn
  OWNER TO opengeo;
