<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "comentarios_perfil".
 *
 * @property int $id
 * @property int $emisor_id
 * @property int $receptor_id
 * @property string $comentario
 * @property string|null $edited_at
 * @property int|null $padre_id
 * @property string $created_at
 *
 * @property ComentariosPerfil $padre
 * @property ComentariosPerfil[] $comentariosPerfils
 * @property Usuarios $emisor
 * @property Usuarios $receptor
 */
class ComentariosPerfil extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'comentarios_perfil';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['emisor_id', 'receptor_id', 'comentario'], 'required'],
            [['emisor_id', 'receptor_id', 'padre_id'], 'default', 'value' => null],
            [['emisor_id', 'receptor_id', 'padre_id'], 'integer'],
            [['comentario'], 'string'],
            [['edited_at', 'created_at'], 'safe'],
            [['padre_id'], 'exist', 'skipOnError' => true, 'targetClass' => ComentariosPerfil::className(), 'targetAttribute' => ['padre_id' => 'id']],
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
            'comentario' => 'Comentario',
            'edited_at' => 'Edited At',
            'padre_id' => 'Padre ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Padre]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPadre()
    {
        return $this->hasOne(ComentariosPerfil::className(), ['id' => 'padre_id']);
    }

    /**
     * Gets query for [[ComentariosPerfils]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComentariosPerfils()
    {
        return $this->hasMany(ComentariosPerfil::className(), ['padre_id' => 'id']);
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
