<?php

namespace app\models;

use app\services\Util;
use Yii;
use yii\base\Model;

class ImagenForm extends Model
{
    public $imagen;

    public function rules()
    {
        return [
            [['imagen'], 'image', 'skipOnEmpty' => false, 'extensions' => ['png', 'jpg', 'jpeg', 'gif']],
        ];
    }

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
