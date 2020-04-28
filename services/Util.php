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

    // Sube o fotos de usuarios o de juegos
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

    public static function s3GetImagenUrl($key, $bucketName)
    {
        $s3 = static::inicializar();
        return $s3->getObjectUrl($bucketName, $key);
    }

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

    // Funciones antiguas
    // public static function s3UploadJuegos($archivo, $key, $bucketName, $archivoAntiguo = null)
    // {
    //     $s3 = static::inicializar();

    //     $nombreArchivo = Yii::getAlias('@uploads/' . $archivo->baseName . '.' . $archivo->extension);
    //     $archivo->saveAs($nombreArchivo);

    //     $imagine = new Imagine();
    //     $image = $imagine->open($nombreArchivo);
    //     $image->resize(new Box(600, 400))->save($nombreArchivo);

    //     if ($archivoAntiguo !== null) {
    //         $s3->deleteObject([
    //             'Bucket' => $bucketName,
    //             'Key' => "juegos/$archivo",
    //         ]);
    //     }

    //     $key .= '.' . $archivo->extension;

    //     return Util::s3SubirJuegos(file_get_contents($nombreArchivo), $key, $bucketName);
    // }

    // public static function s3SubirUsuarios($archivo, $key, $bucketName, $archivoAntiguo = null)
    // {
    //     \yii\imagine\Image::resize($archivo, 200, 200)->save($archivo);

    //     $s3 = static::inicializar();

    //     try {

    //         if (!file_exists('/tmp/tmpfile')) {
    //             mkdir('/tmp/tmpfile');
    //         }

    //         if ($archivoAntiguo !== null) {
    //             static::s3EliminarArchivoUsuarios($archivoAntiguo, $bucketName);
    //         }

    //         $tempFilePath = '/tmp/tmpfile/' . basename($archivo);
    //         $tempFile = fopen($tempFilePath, "w") or die("Error: Unable to open file.");
    //         $fileContents = file_get_contents($archivo);
    //         $tempFile = file_put_contents($tempFilePath, $fileContents);

    //         $s3->putObject([
    //             'Bucket' => $bucketName,
    //             'Key' => "usuarios/$key",
    //             'SourceFile' => $tempFilePath,
    //             'ACL' => 'public-read',
    //         ]);
    //     } catch (S3Exception $e) {
    //         echo $e->getMessage();
    //     } catch (Exception $e) {
    //         echo $e->getMessage();
    //     }
    // }

}
