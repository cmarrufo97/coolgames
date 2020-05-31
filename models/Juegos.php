<?php

namespace app\models;

use app\services\Util;
use Yii;
use yii\web\UploadedFile;

/**
 * This is the model class for table "juegos".
 *
 * @property int $id
 * @property string $titulo
 * @property int $genero_id
 * @property string|null $flanzamiento
 * @property float|null $precio
 * @property string $created_at
 *
 * @property Generos $genero
 */
class Juegos extends \yii\db\ActiveRecord
{
    /**
     * Imagen del juego a subir en Amazon S3.
     *
     * @var [type]
     */
    public $imgUpload;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'juegos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['titulo', 'genero_id'], 'required'],
            [['genero_id'], 'default', 'value' => null],
            [['genero_id'], 'integer'],
            [['flanzamiento', 'created_at'], 'safe'],
            [['precio'], 'number'],
            [['titulo', 'imagen'], 'string', 'max' => 255],
            [['genero_id'], 'exist', 'skipOnError' => true, 'targetClass' => Generos::className(), 'targetAttribute' => ['genero_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'titulo' => 'Titulo',
            'genero_id' => 'Genero ID',
            'flanzamiento' => 'Flanzamiento',
            'precio' => 'Precio',
            'imagen' => 'Imagen',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Genero]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGenero()
    {
        return $this->hasOne(Generos::className(), ['id' => 'genero_id']);
    }

    /**
     * Sube la foto del juego a Amazon S3. Cuando la sube, la borra del local.
     *
     * @return void
     */
    public function uploadImage()
    {
        $this->imgUpload = UploadedFile::getInstance($this, 'imgUpload');

        $origen = Yii::getAlias('@uploads/' . $this->imgUpload->name);

        if ($this->imgUpload !== null) {
            $this->imagen = Util::s3SubirImagen($this->imgUpload, $this->titulo, 'coolgamesyii', $this->imagen, true);
            $this->imgUpload = null;
            unlink($origen);
        }
    }

    /**
     * Obtiene la url de la foto del juego.
     *
     * @return string
     */
    public function getImagen()
    {
        if ($this->imagen !== null) {
            try {
                $ruta = 'juegos/' . $this->imagen;
                $imagen = Util::s3GetImagenUrl($ruta, 'coolgamesyii');
                return $imagen;
            } catch (\Exception $exception) {
            }
        }
        return false;
    }

    /**
     * Obtiene la valoración Media/Global de un juego. Para ello se obtiene todos los votos del juego
     * y se dividen entre los usuarios que han votado.
     *
     * @return float
     */
    public function getValoracionGlobal()
    {
        $votaciones = (float) Valoraciones::find()
            ->where(['=', 'juego_id', $this->id])
            ->sum('estrellas');

        $usuarios = (float) Valoraciones::find()
            ->where(['=', 'juego_id', $this->id])
            ->count('usuario_id');

        if ($usuarios > 0) {
            $resultado = $votaciones / $usuarios;
            return ((float) number_format($resultado, 1));
        }

        return false;
    }

    /**
     * Devuelve la lista de los juegos.
     *
     * @return ActiveQuery
     */
    public static function lista()
    {
        return static::find()
            ->select('*')
            ->indexBy('id')
            ->all();
    }
}
