------------------------------
-- Archivo de base de datos --
------------------------------

DROP TABLE IF EXISTS roles CASCADE;

CREATE TABLE roles
(
    id  bigserial PRIMARY KEY
  , rol varchar(255) NOT NULL UNIQUE
);


DROP TABLE IF EXISTS usuarios CASCADE;

CREATE TABLE usuarios
(
    id          bigserial PRIMARY KEY
  , login       varchar(255) NOT NULL UNIQUE
  , nombre      varchar(255) NOT NULL
  , password    varchar(255) NOT NULL
  , email       varchar(255) NOT NULL UNIQUE
  , auth_key    varchar(255)
  , rol_id      bigint NOT NULL REFERENCES roles (id) DEFAULT 1
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
  , precio          numeric(6,2)
  , created_at      timestamp(0) NOT NULL DEFAULT current_timestamp
);

INSERT INTO roles (rol)
  VALUES ('usuario')
      ,  ('admin');

INSERT INTO usuarios (login,nombre,password,email,rol_id)
    VALUES ('admin','admin',crypt('admin', gen_salt('bf',12)),'admin@admin.com',2);

INSERT INTO usuarios (login,nombre,password,email)
    VALUES ('pepe','pepe',crypt('pepe', gen_salt('bf',12)),'pepe@gmail.com');