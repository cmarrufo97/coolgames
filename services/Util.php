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
                'secret' => Yii::$app->params['amazon']['credentials']['secret']
            ],
            'version' => 'latest',
            'region' => Yii::$app->params['amazon']['region'],
        ]);

        return $s3;
    }


    public static function s3SubirUsuarios($archivo, $key, $bucketName, $archivoAntiguo = null)
    {
        \yii\imagine\Image::resize($archivo, 200, 200)->save($archivo);

        $s3 = static::inicializar();

        try {

            if (!file_exists('/tmp/tmpfile')) {
                mkdir('/tmp/tmpfile');
            }

            if ($archivoAntiguo !== null) {
                static::s3EliminarArchivoUsuarios($archivoAntiguo, $bucketName);
            }

            $tempFilePath = '/tmp/tmpfile/' . basename($archivo);
            $tempFile = fopen($tempFilePath, "w") or die("Error: Unable to open file.");
            $fileContents = file_get_contents($archivo);
            $tempFile = file_put_contents($tempFilePath, $fileContents);

            $s3->putObject([
                'Bucket' => $bucketName,
                'Key' => "usuarios/$key",
                'SourceFile' => $tempFilePath,
            ]);
        } catch (S3Exception $e) {
            echo $e->getMessage();
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public static function s3GetImagenUrl($key, $bucketName)
    {
        $s3 = static::inicializar();

        try {
            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => $bucketName,
                'Key'    => $key,
            ]);

            $request = $s3->createPresignedRequest($cmd, '+30 minutes');
            $signedUrl = (string) $request->getUri();
        } catch (S3Exception $e) {
        }

        return $signedUrl;
    }

    public static function s3GetImagenUsuarioDefecto($key, $bucketName)
    {
        $s3 = static::inicializar();

        try {
            $cmd = $s3->getCommand('GetObject', [
                'Bucket' => $bucketName,
                'Key'    => $key,
            ]);

            $request = $s3->createPresignedRequest($cmd, '+30 minutes');
            $signedUrl = (string) $request->getUri();
        } catch (S3Exception $e) {
        }

        return $signedUrl;
    }

    public static function s3EliminarArchivoUsuarios($archivo, $bucketName)
    {
        $s3 = static::inicializar();

        try {

            $s3->deleteObject([
                'Bucket' => $bucketName,
                'Key' => "usuarios/$archivo"
            ]);
        } catch (S3Exception $e) {
        }
    }
}
