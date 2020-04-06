<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "amigos".
 *
 * @property int $id
 * @property int $usuario_id
 * @property int $amigo_id
 *
 * @property Usuarios $usuario
 * @property Usuarios $amigo
 */
class Amigos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'amigos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['usuario_id', 'amigo_id'], 'required'],
            [['usuario_id', 'amigo_id'], 'default', 'value' => null],
            [['usuario_id', 'amigo_id'], 'integer'],
            [['usuario_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['usuario_id' => 'id']],
            [['amigo_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['amigo_id' => 'id']],
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
            'amigo_id' => 'Amigo ID',
        ];
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

    /**
     * Gets query for [[Amigo]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAmigo()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'amigo_id']);
    }
}
