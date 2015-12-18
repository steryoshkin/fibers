-- Table: fibers.pq

-- DROP TABLE fibers.pq;

CREATE TABLE fibers.pq
(
  id integer NOT NULL DEFAULT nextval('fibers.pq_id_seq'::regclass),
  node integer,
  num integer,
  pq_type_id integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT pq_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.pq
  OWNER TO opengeo;

-- Trigger: add_node_type on fibers.pq

-- DROP TRIGGER add_node_type ON fibers.pq;

CREATE TRIGGER add_node_type
  AFTER INSERT OR UPDATE
  ON fibers.pq
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.add_node_type____();

