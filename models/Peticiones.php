<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "peticiones".
 *
 * @property int $id
 * @property int $emisor_id
 * @property int $receptor_id
 * @property string $created_at
 *
 * @property Usuarios $emisor
 * @property Usuarios $receptor
 */
class Peticiones extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'peticiones';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emisor_id', 'receptor_id'], 'required'],
            [['emisor_id', 'receptor_id'], 'default', 'value' => null],
            [['emisor_id', 'receptor_id'], 'integer'],
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
