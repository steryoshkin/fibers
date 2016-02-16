-- Table: fibers.color

-- DROP TABLE fibers.color;

CREATE TABLE fibers.color
(
  id serial NOT NULL,
  type integer NOT NULL,
  name character varying(100) NOT NULL,
  color character varying(6) NOT NULL,
  descrip character varying(255),
  user_id integer,
  stroke boolean,
  CONSTRAINT color_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.color
  OWNER TO opengeo;
