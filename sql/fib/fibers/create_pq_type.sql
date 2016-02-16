-- Table: fibers.pq_type

-- DROP TABLE fibers.pq_type;

CREATE TABLE fibers.pq_type
(
  id serial NOT NULL,
  name character varying(255),
  type integer,
  ports_num integer,
  unit integer,
  descrip character varying(255),
  user_id integer,
  CONSTRAINT pq_type_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.pq_type
  OWNER TO opengeo;
