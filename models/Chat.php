<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "chat".
 *
 * @property int $id
 * @property int $emisor_id
 * @property int $receptor_id
 * @property string $mensaje
 * @property string $created_at
 *
 * @property Usuarios $emisor
 * @property Usuarios $receptor
 */
class Chat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'chat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emisor_id', 'receptor_id', 'mensaje'], 'required'],
            [['emisor_id', 'receptor_id'], 'default', 'value' => null],
            [['emisor_id', 'receptor_id'], 'integer'],
            [['mensaje'], 'string'],
            [['created_at'], 'safe'],
            [['emisor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['emisor_id' => 'id']],
            [['receptor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Usuarios::className(), 'targetAttribute' => ['receptor_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'emisor_id' => 'Emisor ID',
            'receptor_id' => 'Receptor ID',
            'mensaje' => 'Mensaje',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Emisor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEmisor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'emisor_id']);
    }

    /**
     * Gets query for [[Receptor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReceptor()
    {
        return $this->hasOne(Usuarios::className(), ['id' => 'receptor_id']);
    }
}
