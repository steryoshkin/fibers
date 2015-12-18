-- Table: fibers.users

-- DROP TABLE fibers.users;

CREATE TABLE fibers.users
(
  id serial NOT NULL,
  login character varying(255),
  password character varying(255),
  doc_user character varying(255),
  doc_pass character varying(255),
  agents_user character varying(255),
  agents_pass character varying(255),
  name character varying(255),
  status boolean,
  "group" integer,
  new_pass boolean DEFAULT false,
  CONSTRAINT users_pkey PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE,
  autovacuum_enabled=true
);
ALTER TABLE fibers.users
  OWNER TO opengeo;
REVOKE ALL ON TABLE fibers.users FROM public;
REVOKE ALL ON TABLE fibers.users FROM opengeo;
