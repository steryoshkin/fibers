-- Table: fibers.cable_reserve

-- DROP TABLE fibers.cable_reserve;

CREATE TABLE fibers.cable_reserve
(
  fid serial NOT NULL,
  cable_id integer,
  the_geom geometry(Point,4326),
  CONSTRAINT cable_reserve_pkey PRIMARY KEY (fid),
  CONSTRAINT enforce_dims_the_geom CHECK (st_ndims(the_geom) = 2),
  CONSTRAINT enforce_geotype_the_geom CHECK (geometrytype(the_geom) = 'POINT'::text OR the_geom IS NULL),
  CONSTRAINT enforce_srid_the_geom CHECK (st_srid(the_geom) = 4326)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE fibers.cable_reserve
  OWNER TO opengeo;

-- Index: fibers.spatial_cable_reserve_the_geom

-- DROP INDEX fibers.spatial_cable_reserve_the_geom;

CREATE INDEX spatial_cable_reserve_the_geom
  ON fibers.cable_reserve
  USING gist
  (the_geom);

