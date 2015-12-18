-- Table: fibers.lift

-- DROP TABLE fibers.lift;

CREATE TABLE fibers.lift
(
  id integer NOT NULL DEFAULT nextval('fibers.lift_id_seq'::regclass),
  node_id integer,
  lift_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT lift_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.lift
  OWNER TO opengeo;
