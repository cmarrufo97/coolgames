# Dificultades encontradas

### Comentarios Anidados

Dado que en los comentarios de perfil de los usuarios (donde otros usuarios publican comentarios hacia otros usuarios), se puede responder a otros comentarios la visualización de los mismos no se apreciaba, ya que no se sabia que comentario era respuesta de otro. Para resolver este problema de visualización he tenido que investigar como hacer que, dado un comentario con respuestas, estos se vean anidados, es decir, primero el comentario padre y luego los hijos anidandose. Para ello he investigado y basicamente es que, con ayuda de una clase Comentario voy creando comentarios y añadiendo sus hijos para luego ir recorriendolos, si se da el caso de que tengan hijos pues a esos comentarios hijos se le da un margin-left para que se aprecia la anidación.

### StarRating Krajee
El plugin de StarRating es el plugin de krajee mediante el cual puedes realizar la votación de un juego concreto a través de las típicas estrellas (como en Google Play,e tc). El problema que he tenido ha sido que, el plugin del krajee generaba el mismo id en el widget, entonces, dado que en la tienda muestro el plugin por cada juego, me daba errores en la consola. Para ello he tenido que investigar por foros de yii2 framework como poder cambiar atributos del formulario que envía la valoración sin que perdiese la validación del modelo.

### PayPal de bitcko/yii2-bitcko-paypal-api
Para implementar el pago por PayPal he usado el plugin de bitcko, mencionado en el título.

El principal problema que he tenido aquí ha sido que, en el ejemplo que muestra el autor del plugin, siempre mete el mismo valor a cobrar. Es decir, en las dos acciones (checkout y payment) que necesita el plugin la variable $params siempre deben coincidir en el precio para que el pago se realize de verdad, es decir, que se reste, y, dado que cada juego tiene un precio, pues esto era un problema. Para solucionar este problema, he usado el $_SESSION para pasar de una acción a otra, y, una vez se realiza el pago y demás gestiones, la borro.

---

# Elementos de innovación

#### AWS S3

He usado el servicio de Amazon S3 para el almacenamiento web.

#### PayPal

Pagos de los juegos mediante la API de PayPal. Dichos pagos se realizan de manera ficticia, con las cuentas sandbox que PayPal proporciona al crear una app. Pero en el día de mañana si disponemos de una cuenta business y las credenciales para acceder a la API, pues esto funcionaría perfectamente igual.


