-- Table: fibers.node

-- DROP TABLE fibers.node;

CREATE TABLE fibers.node
(
  id serial NOT NULL,
  address character varying(255),
  address_full character varying(255),
  incorrect boolean,
  street_id integer,
  street_num_id integer,
  num_ent integer,
  location_id integer,
  room_id integer,
  descrip character varying(255),
  user_id integer,
  the_geom geometry(Point,4326),
  type integer DEFAULT 0,
  loc_text character varying(100),
  is_new boolean NOT NULL DEFAULT true,
  u_const boolean DEFAULT true,
  date timestamp without time zone,
  node_type_id integer,
  CONSTRAINT node_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.node
  OWNER TO opengeo;

-- Function: fibers.node_set_default_trigger_function()

-- DROP FUNCTION fibers.node_set_default_trigger_function();

CREATE OR REPLACE FUNCTION fibers.node_set_default_trigger_function()
  RETURNS trigger AS
$BODY$
BEGIN
  IF (NEW.is_new IS NULL OR NEW.is_new = FALSE) AND (NEW.u_const IS NULL OR NEW.u_const = FALSE) THEN
    NEW.is_new := TRUE;
    NEW.u_const := TRUE;
    NEW.date := ('now'::text)::timestamp;
  END IF;
  RETURN NEW;
END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fibers.node_set_default_trigger_function()
  OWNER TO opengeo;

-- Trigger: node_set_default_trigger on fibers.node

-- DROP TRIGGER node_set_default_trigger ON fibers.node;

CREATE TRIGGER node_set_default_trigger
  BEFORE INSERT
  ON fibers.node
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.node_set_default_trigger_function();

-- Function: fibers.node_update_trigger_function()

-- DROP FUNCTION fibers.node_update_trigger_function();

CREATE OR REPLACE FUNCTION fibers.node_update_trigger_function()
  RETURNS trigger AS
$BODY$
BEGIN
  IF NEW.type = 1 AND NEW.u_const = TRUE THEN
    NEW.u_const := NULL;
  END IF;
  RETURN NEW;
END$BODY$
  LANGUAGE plpgsql VOLATILE
  COST 100;
ALTER FUNCTION fibers.node_update_trigger_function()
  OWNER TO opengeo;

-- Trigger: node_update_trigger on fibers.node

-- DROP TRIGGER node_update_trigger ON fibers.node;

CREATE TRIGGER node_update_trigger
  BEFORE UPDATE
  ON fibers.node
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.node_update_trigger_function();
