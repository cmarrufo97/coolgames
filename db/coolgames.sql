------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id          bigserial PRIMARY KEY
  , login       varchar(255) NOT NULL UNIQUE
  , nombre      varchar(255) NOT NULL
  , password    varchar(255) NOT NULL
  , email       varchar(255) NOT NULL UNIQUE
  , auth_key    varchar(255)
  , rol         varchar(255)
  , token       varchar(255)
  , created_at  timestamp(0) NOT NULL DEFAULT current_timestamp
);

DROP TABLE IF EXISTS generos CASCADE;

CREATE TABLE generos
(
    id          bigserial PRIMARY KEY
  , denom       varchar(255) NOT NULL UNIQUE
  , created_at  timestamp(0) NOT NULL DEFAULT current_timestamp
);

DROP TABLE IF EXISTS juegos CASCADE;

CREATE TABLE juegos 
(
    id              bigserial PRIMARY KEY
  , titulo          varchar(255) NOT NULL
  , genero_id       bigint NOT NULL REFERENCES generos (id)
  , flanzamiento    date
  , created_at      timestamp(0) NOT NULL DEFAULT current_timestamp
);

INSERT INTO usuarios (login,nombre,password,email,rol)
    VALUES ('admin','admin',crypt('admin', gen_salt('bf',12)),'admin@admin.com','admin');