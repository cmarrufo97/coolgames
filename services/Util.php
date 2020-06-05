<?php

namespace app\services;

use app\models\Carrito;
use app\models\Compras;
use app\models\Juegos;
use app\models\Usuarios;
use Aws\S3\Exception\S3Exception;
use Aws\S3\S3Client;
use Exception;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Yii;

class Util
{

    /**
     * Inicializa el cliente de Amazon S3.
     *
     * @return S3Client
     */
    protected static function inicializar()
    {
        $s3 = S3Client::factory([
            'credentials' => [
                'key' => Yii::$app->params['amazon']['credentials']['key'],
                'secret' => Yii::$app->params['amazon']['credentials']['secret'],
            ],
            'version' => 'latest',
            'region' => 'eu-west-3',
        ]);

        return $s3;
    }

    /**
     * Sube fotos de usuarios o juegos (si la variable $esJuego es true).
     *
     * @param [type] $archivo
     * @param [type] $key
     * @param [type] $bucketName
     * @param [type] $archivoAntiguo
     * @param boolean $esJuego
     * @return void
     */
    public static function s3SubirImagen($archivo, $key, $bucketName, $archivoAntiguo = null, $esJuego = false)
    {

        $s3 = static::inicializar();

        if ($esJuego === true) {
            $nombreArchivo = Yii::getAlias('@uploads/' . $archivo->baseName . '.' . $archivo->extension);
            $archivo->saveAs($nombreArchivo);

            $imagine = new Imagine();
            $image = $imagine->open($nombreArchivo);
            $image->resize(new Box(100, 150))->save($nombreArchivo);

            if ($archivoAntiguo !== null) {
                $s3->deleteObject([
                    'Bucket' => $bucketName,
                    'Key' => "juegos/$archivo",
                ]);
            }

            $key .= '.' . $archivo->extension;

            try {
                $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key' => "juegos/$key",
                    'Body' => $archivo,
                    'ACL' => 'public-read',
                ]);
            } catch (S3Exception $e) {
                echo $e->getMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
            return Util::s3SubirJuegos(file_get_contents($nombreArchivo), $key, $bucketName);
        } else {
            \yii\imagine\Image::resize($archivo, 200, 200)->save($archivo);
            try {

                if (!file_exists('/tmp/tmpfile')) {
                    mkdir('/tmp/tmpfile');
                }

                if ($archivoAntiguo !== null) {
                    $s3->deleteObject([
                        'Bucket' => $bucketName,
                        'Key' => "usuarios/$archivoAntiguo"
                    ]);
                }

                $tempFilePath = '/tmp/tmpfile/' . basename($archivo);
                $tempFile = fopen($tempFilePath, "w") or die("Error: Unable to open file.");
                $fileContents = file_get_contents($archivo);
                $tempFile = file_put_contents($tempFilePath, $fileContents);

                $s3->putObject([
                    'Bucket' => $bucketName,
                    'Key' => "usuarios/$key",
                    'SourceFile' => $tempFilePath,
                    'ACL' => 'public-read',
                ]);
            } catch (S3Exception $e) {
                echo $e->getMessage();
            } catch (Exception $e) {
                echo $e->getMessage();
            }
        }
    }

    /**
     * Obtiene la url de una imagen.
     *
     * @param [type] $key
     * @param [type] $bucketName
     * @return string
     */
    public static function s3GetImagenUrl($key, $bucketName)
    {
        $s3 = static::inicializar();
        return $s3->getObjectUrl($bucketName, $key);
    }

    /**
     * Sube la fotos de los juegos a la carpeta /juegos ubicada en el bucket
     * de Amazon S3.
     *
     * @param [type] $archivo
     * @param [type] $key
     * @param [type] $bucketName
     * @return void
     */
    public static function s3SubirJuegos($archivo, $key, $bucketName)
    {
        $s3 = static::inicializar();

        try {
            $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => "juegos/$key",
                'Body' => $archivo,
                'ACL' => 'public-read',
            ]);
        } catch (S3Exception $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }

        return $key;
    }

    /**
     * Descarga una foto de Amazon S3.
     *
     * @param [type] $key
     * @param [type] $fileName
     * @param [type] $bucketName
     * @return void
     */
    public static function s3Descargar($key, $fileName, $bucketName)
    {
        $s3 = static::inicializar();
        // TODO: HACER QUE SE DESCARGUE EL JUEGO
        $result = $s3->getObject(array(
            'Bucket' => $bucketName,
            'Key'    => $key,
        ));

        header("Content-Type: {$result['ContentType']}");
        header("Content-Disposition: attachment; filename=$fileName");
        echo $result['Body'];
    }

    /**
     * Realiza la compra de juegos a través de paypal y envía email de factura.
     *
     * @return void
     */
    public static function realizarCompra()
    {
        $params = [];
        $params = $_SESSION['params'];
        $juegos = $_SESSION['params']['juegos'];
        $usuario_id = Yii::$app->user->id;
        $usuario = Usuarios::findOne($usuario_id);

        Yii::$app->PayPalRestApi->processPayment($params);
        $response = Yii::$app->response;

        if (!empty($response->data) && $response->data->state === 'approved') {
            if (count($juegos) > 0) {
                $tabla = "<table id='customers'>
                    <tr>
                        <th>Cantidad</th>
                        <th>Producto</th>
                        <th>Fecha de compra</th>
                        <th>Subtotal</th>
                        <th>Total</th>
                      </tr>";
                $totalDeducir = (float) 0;
                foreach ($juegos as $juego) {
                    $modelJuego = Juegos::findOne($juego);
                    $fecha = Yii::$app->formatter->asDate(date('d-m-Y'));
                    $precio = Yii::$app->formatter->asCurrency($modelJuego->precio);

                    $estaEnCarrito = Carrito::find()->where(['usuario_id' => $usuario_id])
                        ->andFilterWhere(['juego_id' => $modelJuego->id])->exists();

                    $tabla .=
                        "
                            <tr>
                            <td>1</td>
                            <td>$modelJuego->titulo</td>
                            <td>$fecha</td>
                            <td>$precio</td>
                            <td>$precio</td>
                            </tr>";
                    $totalDeducir += (float) $modelJuego->precio;

                    $modelCompras = new Compras();
                    $modelCompras->usuario_id = $usuario_id;
                    $modelCompras->juego_id = $modelJuego->id;
                    $modelCompras->subtotal = $modelJuego->precio;
                    $modelCompras->total = $modelJuego->precio;
                    if ($modelCompras->save()) {
                        if ($estaEnCarrito) {
                            $modelCarrito = Carrito::find()->select('id')
                                ->where(['usuario_id' => $usuario_id])
                                ->andFilterWhere(['juego_id' => $modelJuego->id])->one();
                            $modelCarrito->delete();
                        }
                    }
                }
                $tabla .= '</table>';
                $mensaje = <<<EOT
                    <!DOCTYPE html>
                    <html>
                    <head>
                    <style>
                    #customers {
                      font-family: "Trebuchet MS", Arial, Helvetica, sans-serif;
                      border-collapse: collapse;
                      width: 100%;
                    }
                    
                    #customers td, #customers th {
                      border: 1px solid #ddd;
                      padding: 8px;
                    }
                    
                    #customers tr:nth-child(even){background-color: #f2f2f2;}
                    
                    #customers tr:hover {background-color: #ddd;}
                    
                    #customers th {
                      padding-top: 12px;
                      padding-bottom: 12px;
                      text-align: left;
                      background-color: #4CAF50;
                      color: white;
                    }
                    </style>
                    </head>
                    <body>
                    <h1>Hola, $usuario->login</h1>
                    <p>Muchisimas gracias por su compra, aqui tiene los detalles de su compra:</p>
                    $tabla
                    </body>
                    </html>
                    EOT;
                Yii::$app->mailer->compose()
                    ->setFrom(Yii::$app->params['smtpUsername'])
                    ->setTo($usuario->email)
                    ->setSubject('Factura de compra')
                    ->setHtmlBody($mensaje)
                    ->send();
            }
            unset($_SESSION['params']);
            Yii::$app->session->setFlash('success', 'Pago realizado con éxito.');
        }
    }
}
