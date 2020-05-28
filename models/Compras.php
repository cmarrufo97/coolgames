<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "compras".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $juego_id
 * @property float|null $subtotal
 * @property float|null $total
 * @property string $created_at
 *
 * @property Juegos $juego
 * @property Usuarios $usuario
 */
class Compras extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'compras';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'juego_id'], 'required'],
            [['usuario_id', 'juego_id'], 'default', 'value' => null],
            [['usuario_id', 'juego_id'], 'integer'],
            [['subtotal', 'total'], 'number'],
            [['created_at'], 'safe'],
            [['juego_id'], 'exist', 'skipOnError' => true, 'targetClass' => Juegos::className(), 'targetAttribute' => ['juego_id' => 'id']],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'usuario_id' => 'Usuario ID',
            'juego_id' => 'Juego ID',
            'subtotal' => 'Subtotal',
            'total' => 'Total',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Juego]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getJuego()
    {
        return $this->hasOne(Juegos::className(), ['id' => 'juego_id']);
    }

    /**
     * Gets query for [[Usuario]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'usuario_id']);
    }
}
