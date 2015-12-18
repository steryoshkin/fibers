-- Table: fibers.pq_schem

-- DROP TABLE fibers.pq_schem;

CREATE TABLE fibers.pq_schem
(
  id serial NOT NULL,
  pq_id integer,
  name character varying(255),
  data bytea,
  date timestamp without time zone DEFAULT ('now'::text)::timestamp without time zone,
  user_id integer,
  CONSTRAINT pq_schem_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.pq_schem
  OWNER TO opengeo;
GRANT ALL ON TABLE fibers.pq_schem TO opengeo;
