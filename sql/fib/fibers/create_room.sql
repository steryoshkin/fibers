-- Table: fibers.room

-- DROP TABLE fibers.room;

CREATE TABLE fibers.room
(
  id serial NOT NULL,
  room character varying(255),
  descrip character varying(255),
  user_id integer,
  CONSTRAINT room_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.room
  OWNER TO opengeo;
