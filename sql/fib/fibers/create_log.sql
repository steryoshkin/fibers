-- Table: fibers.log

-- DROP TABLE fibers.log;

CREATE TABLE fibers.log
(
  id serial NOT NULL,
  table_name character varying(100),
  table_id integer NOT NULL,
  data_old character varying(1000),
  user_id integer,
  date timestamp without time zone DEFAULT ('now'::text)::timestamp without time zone,
  CONSTRAINT log_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.log
  OWNER TO opengeo;
