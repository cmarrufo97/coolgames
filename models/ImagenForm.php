<?php

namespace app\models;

use app\services\Util;
use Yii;
use yii\base\Model;

/**
 * Clase encarga de subir la foto de perfil de un usuario.
 */
class ImagenForm extends Model
{
    public $imagen;

    public function rules()
    {
        return [
            [['imagen'], 'image', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'jpeg', 'gif']],
        ];
    }

    /**
     * Función para subir la foto de perfil de un usuario. En primer lugar se sube a local, pero luego se sube al bucket disponible en Amazon S3. Finalmente cuando la foto es subida a Amazon S3, es eliminada del local.
     *
     * @param [type] $id
     * @param [type] $nombre
     * @param [type] $bucketName
     * @return void
     */
    public function uploadUserImage($id, $nombre, $bucketName)
    {
        $imagenAntigua = Usuarios::find()
            ->select('imagen')->where(['=', 'id', $id])->scalar();

        if ($this->validate()) {
            $filename = $id . $nombre . '.' . $this->imagen->extension;
            $origen = Yii::getAlias('@uploads/' . $filename);
            $this->imagen->saveAs($origen);
            //subir a S3
            // Util::s3SubirUsuarios($origen, $filename, $bucketName, $imagenAntigua);
            Util::s3SubirImagen($origen,$filename,$bucketName,$imagenAntigua);

            unlink($origen);

            return true;
        } else {
            return false;
        }
    }
}
