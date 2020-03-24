<?php

namespace app\models;

use Yii;

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
            [['titulo'], 'string', 'max' => 255],
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
}
