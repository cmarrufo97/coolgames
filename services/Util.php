<?php

namespace app\services;

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
}
