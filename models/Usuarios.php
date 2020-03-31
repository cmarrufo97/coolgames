<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "usuarios".
 *
 * @property int $id
 * @property string $login
 * @property string $nombre
 * @property string $password
 * @property string $email
 * @property string|null $auth_key
 * @property string|null $rol
 * @property string|null $token
 * @property string $created_at
 * 
 * @property Roles $rol
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';
    public $password_repeat;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'usuarios';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['login', 'nombre', 'password', 'email'], 'required'],
            [['rol_id'], 'default', 'value' => null],
            [['rol_id'], 'integer'],
            [['created_at'], 'safe'],
            [['login', 'nombre', 'password', 'email', 'auth_key', 'rol', 'token'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['login'], 'unique'],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['rol_id' => 'id']],
            [
                ['password'],
                'required',
                'on' => [self::SCENARIO_DEFAULT, self::SCENARIO_CREAR],
            ],
            [
                ['password'],
                'trim',
                'on' => [self::SCENARIO_CREAR],
            ],
            [
                ['password_repeat'],
                'required',
                'on' => [self::SCENARIO_CREAR]
            ],
            [
                ['password_repeat'],
                'compare',
                'compareAttribute' => 'password',
                'skipOnEmpty' => false,
                'on' => [self::SCENARIO_CREAR],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'login' => 'Login',
            'nombre' => 'Nombre',
            'password' => 'Contraseña',
            'password_repeat' => 'Repetir contraseña',
            'email' => 'Correo electrónico',
            'auth_key' => 'Auth Key',
            'rol_id' => 'Rol ID',
            'token' => 'Token',
            'created_at' => 'Created At',
        ];
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
    }

    /**
     * Obtiene el id del usuario.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return $this->auth_key;
    }

    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }

    /**
     * Gets query for [[Rol]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRol()
    {
        return $this->hasOne(Roles::className(), ['id' => 'rol_id']);
    }

    /**
     * Buscar al usuario por su nombre
     *
     * @param [type] $nombre
     * @return static|null instancia de ActiveRecord si coincide o null si no coincide.
     */
    public static function findPorNombre($nombre)
    {
        return static::findOne(['nombre' => $nombre]);
    }

    /**
     * Buscar al usuario por su login, es decir, por la columna login de la base de datos.
     *
     * @param [type] $login
     * @return static|null instancia de ActiveRecord si coincide o null si no.
     */
    public static function findPorLogin($login)
    {
        return static::findOne(['login' => $login]);
    }

    /**
     * Busca al usuario por su email.
     *
     * @param [type] $email
     * @return static|null instancia de ActiveRecord si coincide o null si no.
     */
    public static function findPorEmail($email)
    {
        return static::findOne(['email' => $email]);
    }

    /**
     * Valida la contraseña del usuario
     *
     * @param [type] $password
     * @return true|false si coincide o no la contraseña
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            if ($this->scenario === self::SCENARIO_CREAR) {
                $security = Yii::$app->security;
                $this->auth_key = $security->generateRandomString();
                $this->password = $security->generatePasswordHash($this->password);
                // asignar rol por defecto (1 -> usuario)
                $this->rol_id = 1;
                // asignar al usuario recien registrado un token
                $this->token = $security->generateRandomString();
            }
        }
        return true;
    }
}
