<?php

namespace app\models;

use app\services\Util;
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
 * @property string|null $cod_verificacion
 * @property string|null $imagen
 * @property string $created_at
 * 
 * @property Roles $rol
 */
class Usuarios extends \yii\db\ActiveRecord implements IdentityInterface
{
    const SCENARIO_CREAR = 'crear';
    const SCENARIO_CREATE = 'create';
    const SCENARIO_RECUPERAR = 'recuperar';
    const SCENARIO_MODIFICAR = 'modificar';
    public $password_repeat;

    const IMAGEN = '@img/usuario-defecto.png';


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
            [['rol_id', 'estado_id'], 'default', 'value' => null],
            [['rol_id', 'estado_id'], 'integer'],
            [['imagen'], 'string'],
            [['created_at'], 'safe'],
            [['login', 'nombre', 'password', 'email', 'auth_key', 'token', 'cod_verificacion'], 'string', 'max' => 255],
            [['email'], 'unique'],
            [['login'], 'unique'],
            [['estado_id'], 'exist', 'skipOnError' => true, 'targetClass' => Estados::className(), 'targetAttribute' => ['estado_id' => 'id']],
            [['rol_id'], 'exist', 'skipOnError' => true, 'targetClass' => Roles::className(), 'targetAttribute' => ['rol_id' => 'id']],
            [
                ['password'],
                'required',
                'on' => [
                    self::SCENARIO_DEFAULT,
                    self::SCENARIO_CREAR,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_RECUPERAR,
                    self::SCENARIO_MODIFICAR,
                ],
            ],
            [
                ['password'],
                'trim',
                'on' => [
                    self::SCENARIO_CREAR,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_RECUPERAR,
                    self::SCENARIO_MODIFICAR,
                ],
            ],
            [
                ['password_repeat'],
                'required',
                'on' => [
                    self::SCENARIO_CREAR,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_RECUPERAR,
                    self::SCENARIO_MODIFICAR,
                ]
            ],
            [
                ['password_repeat'],
                'compare',
                'compareAttribute' => 'password',
                'skipOnEmpty' => false,
                'on' => [
                    self::SCENARIO_CREAR,
                    self::SCENARIO_CREATE,
                    self::SCENARIO_RECUPERAR,
                    self::SCENARIO_MODIFICAR,
                ],
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
            'estado_id' => 'Estado ID',
            'token' => 'Token',
            'cod_verificacion' => 'Cod Verificacion',
            'imagen' => 'Imagen',
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

    /**
     * Método que valida si el usuario que entra tiene token.
     * Si el usuario tiene token, es que no ha confirmado su cuenta, por lo tanto,
     * no podrá acceder hasta que la confirme.
     *
     * @return boolean
     */
    public static function tieneToken($login)
    {
        return (new \yii\db\Query())
            ->select(['login'])
            ->from('usuarios')
            ->where("login = '$login' and token is distinct from null")
            ->all();
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($this->scenario === self::SCENARIO_MODIFICAR) {
            $security = Yii::$app->security;
            $this->password = $security->generatePasswordHash($this->password);
            $this->token = null;
        }

        if ($insert) {
            if ($this->scenario === self::SCENARIO_CREAR) {
                $security = Yii::$app->security;
                $this->auth_key = $security->generateRandomString();
                $this->password = $security->generatePasswordHash($this->password);
                // asignar rol por defecto (1 -> usuario)
                $this->rol_id = 1;

                $this->estado_id = (int) Estados::find()->select('id')->where(['=', 'estado', 'desconectado'])->scalar();

                // asignar al usuario recien registrado un token
                $this->token = $security->generateRandomString();
            }

            if ($this->scenario === self::SCENARIO_CREATE) {
                $security = Yii::$app->security;
                $this->auth_key = $security->generateRandomString();
                $this->password = $security->generatePasswordHash($this->password);
                $this->token = null;
            }
        }
        return true;
    }

    public function getAmigos()
    {
        $directa = Yii::$app->db->createCommand("SELECT * 
        FROM usuarios WHERE id IN (SELECT amigo_id FROM amigos WHERE usuario_id = :usuario_id)")
            ->bindValue(':usuario_id', $this->id)
            ->queryAll();

        $inversa = Yii::$app->db->createCommand("SELECT * 
            FROM usuarios WHERE id IN (SELECT usuario_id FROM amigos WHERE amigo_id = :amigo_id)")
            ->bindValue(':amigo_id', $this->id)
            ->queryAll();

        $amigos = array_unique(array_merge($directa, $inversa), SORT_REGULAR);

        if (!empty($amigos)) {
            return $amigos;
        }

        return [];
    }

    public static function esAmigo($amigo_id)
    {
        $directa = Amigos::find()->where(['=', 'usuario_id', Yii::$app->user->id])
            ->andFilterWhere(['=', 'amigo_id', $amigo_id])
            ->exists();

        $inversa = Amigos::find()->where(['=', 'usuario_id', $amigo_id])
            ->andFilterWhere(['=', 'amigo_id', Yii::$app->user->id])
            ->exists();

        if ($directa || $inversa) {
            return true;
        }

        return false;
    }

    public function getImagen()
    {
        if ($this->imagen !== null) {
            try {
                $ruta = 'usuarios/' . $this->imagen;
                $imagen = Util::s3GetImagenUrl($ruta, 'coolgamesyii');
                return $imagen;
            } catch (\Exception $exception) {
            }
        } else {
            try {
                $ruta = 'usuarios/' . basename(static::IMAGEN);
                $imagenDefecto = Util::s3GetImagenUrl($ruta, 'coolgamesyii');
                return $imagenDefecto;
            } catch (\Exception $e) {
            }
        }
        return false;
    }

    public function getCountAmigos()
    {
        return count($this->getAmigos());
    }

    public function getItemsCarrito()
    {
        return Carrito::find()->where(['usuario_id' => $this->id])->count('juego_id');
    }
}
