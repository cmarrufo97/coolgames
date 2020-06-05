# Instrucciones de instalación y despliegue

## En local

Debes disponer de:

* PHP 7.4
* PostgreSQL
* Composer
* Cuenta de Amazon S3 disponible.
* Email.
* Cuenta de PayPal para recibir los pagos.

Instalación:

<ol>
<li>
Creamos un directorio <code>coolgames</code> y vamos a él.
</li>

<li>
Ejecutar los siguientes comandos:

```
git clone https://github.com/cmarrufo97/coolgames.git .
composer install
```

</li>

<li>
Cambiamos el email en el <code>./config/params.php</code>

```
'smtpUsername' => 'Asignamos aqui el correo que mandará los emails de registro, etc.'
```

</li>

<li>

Crear las variables de entorno en el archivo <code>.env</code>:

* SMT_PASS con la clave de aplicación de correo.
* S3\__KEY\__ la key de Amazon S3.
* S3_SECRET la secret de Amazon S3.
* PAYPAL_ID El ID que nos da PayPal.
* PAYPAL_SECRET El SECRET que nos da PayPal.

</li>

<li>

Creamos y volcamos la base de datos:

```
db/create.sh
db/load.sh
```

</li>

<li>
Ejecutamos <code>make serve</code> para desplegar servidor local.
</li>

<li>
Accedemos al servidor en el navegador poniendo en la url <code>localhost:8080</code>
</li>

</ol>

## En la nube

Para desplegar la aplicación necesitas como requisito el Heroku Cli

Despliegue:

1. Realizamos el forkeo del repositorio:
https://github.com/cmarrufo97/coolgames

2. Creamos una app en heroku y la enlazamos con el repositorio.

3. Añadimos el addon de Postgres de Heroku.

4. Mediante la terminal logueamos con heroku y volcamos la base de datos del proyecto a la base de datos postgres de heroku.

```
heroku login
heroku pg:psql < db/coolgames.git
```


5. Configuramos las variables de entorno (como hicimos anteriormente en local):
    * DATABASE_URL la URL de la base de datos del 3er paso.
    * YII_ENV a prod. (producción) o el modo que queramos.
    * SMT_PASS con la clave de aplicación de correo.
    * S3\__KEY\__ la key de Amazon S3.
    * S3_SECRET la secret de Amazon S3.
    * PAYPAL_ID El ID que nos da PayPal.
    * PAYPAL_SECRET El SECRET que nos da PayPal.

6. La aplicación ya está lista para usarse.