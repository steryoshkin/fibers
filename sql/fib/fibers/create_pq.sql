-- Table: fibers.pq

-- DROP TABLE fibers.pq;

CREATE TABLE fibers.pq
(
  id serial NOT NULL,
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

-- Function: fibers.add_node_type____()

-- DROP FUNCTION fibers.add_node_type____();

CREATE OR REPLACE FUNCTION fibers.add_node_type____()
  RETURNS trigger AS
$BODY$DECLARE
    node_type integer;
BEGIN
    if(NEW.id>0) THEN
    node_type = (SELECT pt1.type FROM fibers.pq AS p1, fibers.pq_type AS pt1 WHERE p1.node = NEW.node AND p1.pq_type_id = pt1.id LIMIT 1);
    UPDATE fibers.node SET type = node_type  WHERE id=NEW.id;
    RETURN NEW;
    END IF;
END;$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fibers.add_node_type____()
  OWNER TO opengeo;

-- Trigger: add_node_type on fibers.pq

-- DROP TRIGGER add_node_type ON fibers.pq;

CREATE TRIGGER add_node_type
  AFTER INSERT OR UPDATE
  ON fibers.pq
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.add_node_type____();
