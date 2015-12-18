-- Table: fibers.node

-- DROP TABLE fibers.node;

CREATE TABLE fibers.node
(
  id integer NOT NULL DEFAULT nextval('fibers.node_id_seq'::regclass),
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
  loc_text character varying(100) COLLATE pg_catalog."ru_RU.utf8",
  is_new boolean NOT NULL DEFAULT true,
  u_const boolean DEFAULT true,
  date timestamp without time zone,
  CONSTRAINT node_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.node
  OWNER TO opengeo;

-- Trigger: node_set_default_trigger on fibers.node

-- DROP TRIGGER node_set_default_trigger ON fibers.node;

CREATE TRIGGER node_set_default_trigger
  BEFORE INSERT
  ON fibers.node
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.node_set_default_trigger_function();

-- Trigger: node_update_trigger on fibers.node

-- DROP TRIGGER node_update_trigger ON fibers.node;

CREATE TRIGGER node_update_trigger
  BEFORE UPDATE
  ON fibers.node
  FOR EACH ROW
  EXECUTE PROCEDURE fibers.node_update_trigger_function();

