<?php

namespace app\controllers;

use app\models\ComentariosPerfil;
use app\models\ImagenForm;
use app\models\Roles;
use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\UploadedFile;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
                'class' => AccessControl::class,
                'only' => ['registrar'],
                'rules' => [
                    // allow authenticated users
                    [
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    // everything else is denied by default
                ],
            ],

            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rules, $action) {
                            $adminId = Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar();

                            $usuario_rol_id = Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar();

                            return $usuario_rol_id === $adminId;
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Usuarios models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new UsuariosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Usuarios model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Usuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Usuarios(['scenario' => Usuarios::SCENARIO_CREATE]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Usuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    public function actionRegistrar()
    {
        $model = new Usuarios(['scenario' => Usuarios::SCENARIO_CREAR]);


        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $url = Url::to([
                'usuarios/confirmar',
                'token' => $model->token,
            ], true);

            $mensaje = <<<EOT
            <h1>Hola, $model->login, pinche en el enlace siguiente para confirmar su cuenta:</h1>
            <a href="$url">Púlse aqui para confirmar su cuenta.</a>
            EOT;

            if ($this->actionCorreo($model->email, 'Confirmación de cuenta', $mensaje)) {
                Yii::$app->session->setFlash('success', 'Cuenta creada correctamente.
                Se envío un email de confirmación de cuenta.');
            } else {
                Yii::$app->session->setFlash('error', 'No se ha podido enviar el email de confirmación');
            }

            return $this->redirect(['site/login']);
        }

        return $this->render('registrar', [
            'model' => $model,
        ]);
    }

    public function actionCorreo($email, $asunto, $mensaje)
    {
        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['smtpUsername'])
            ->setTo($email)
            ->setSubject($asunto)
            ->setHtmlBody($mensaje)
            ->send();
    }

    public function actionConfirmar($token)
    {
        $sent = Yii::$app->db->createCommand("UPDATE usuarios SET token = null WHERE token = :valor")
            ->bindValue(':valor', $token);

        if ($sent->execute()) {
            Yii::$app->session->setFlash('success', 'Cuenta confirmada');
        } else {
            Yii::$app->session->setFlash('error', 'No se pudo confirmar la cuenta');
        }

        return $this->redirect(['site/login']);
    }

    public function actionImagen($id)
    {
        if ($id != Yii::$app->user->id) {
            Yii::$app->session->setFlash('error', 'No puede modificar la foto de otro usuario');
            return $this->goBack();
        }

        $usuario_id = $id;
        $nombre = Usuarios::find()->select('login')->where(['=', 'id', $usuario_id])->scalar();
        $model = new ImagenForm();
        $userModel = $this->findModel($usuario_id);

        if (Yii::$app->request->isPost) {
            $model->imagen = UploadedFile::getInstance($model, 'imagen');
            if ($model->uploadUserImage($usuario_id, $nombre, 'coolgamesyii')) {
                // Yii::debug('Se sube');

                $filename = $usuario_id . $nombre . '.' . $model->imagen->extension;

                $userModel->imagen = $filename;
                $userModel->save();
                Yii::$app->session->setFlash('success', 'Foto de perfil modificada correctamente.');
                return $this->redirect(['usuarios/perfil']);
            }
        }

        return $this->render('imagen', [
            'model' => $model,
        ]);
    }

    public function actionPerfil($id = null)
    {
        if ($id === null && Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if ($id == null && !Yii::$app->user->isGuest) {
            $id = Yii::$app->user->id;
        }

        $model = $this->findModel($id);
        $model->scenario = Usuarios::SCENARIO_MODIFICAR;
        $modelPerfil = new ComentariosPerfil();

        $comentariosRecibidos = ComentariosPerfil::find()->where(['=', 'receptor_id', $id])
            ->orderBy(['created_at' => SORT_DESC])
            ->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Perfil modificado correctamente.');
        }

        $model->password = '';

        return $this->render('perfil', [
            'model' => $model,
            'modelPerfil' => $modelPerfil,
            'comentariosRecibidos' => $comentariosRecibidos,
        ]);
    }

    /**
     * Acccion que pide el email al usuario para poder iniciar la recuperación
     * de la contraseña de la cuenta
     * @param [type] $id
     * @param [type] $codigoVerificacion
     * @return void
     */
    public function actionResetear()
    {
        $model = new Usuarios(['scenario' => Usuarios::SCENARIO_RECUPERAR]);


        if (Yii::$app->request->isPost) {
            $email = Yii::$app->request->post()['Usuarios']['email'];
            $existeEmail = Usuarios::findPorEmail($email);
            if ($existeEmail) {
                $modelUsuario = Usuarios::findPorEmail($email);
                $codVerificacion = substr(md5(uniqid(mt_rand(), true)), 0, 8);
                $modelUsuario->cod_verificacion = $codVerificacion;

                if ($modelUsuario->save()) {
                    $usuario_id = $modelUsuario->id;

                    $url = Url::to([
                        'usuarios/recuperar',
                        'id' => $usuario_id,
                        'codigoVerificacion' => $codVerificacion,
                    ], true);

                    $mensaje = <<<EOT
                    <h1>Hola, pinche en el siguiente enlace para recuperar su contraseña </h1>
                    <a href="$url">Púlse aqui para recuperar su contraseña.</a>
                    EOT;

                    if ($this->actionCorreo($email, 'Recuperación de contraseña', $mensaje)) {
                        Yii::$app->session->setFlash('success', 'Se envió el correo de recuperación de contraseña correctamente');
                    } else {
                        Yii::$app->session->setFlash('error', 'Se produjo un error al enviar el correo de recuperación de contraseña');
                    }
                }
            } else {
                Yii::$app->session->setFlash('error', 'El email introducido no se encontró en el sistema');
            }
        }



        return $this->render('resetear', [
            'model' => $model,
        ]);
    }

    /**
     * Acción que se encarga de modificar la contraseña de la cuenta.
     * @param [type] $id
     * @param [type] $codigoVerificacion
     * @return void
     */
    public function actionRecuperar($id, $codigoVerificacion)
    {
        $model = Usuarios::findOne($id);
        $model->scenario = Usuarios::SCENARIO_RECUPERAR;

        $correcto = Usuarios::find()->where("id = $model->id AND cod_verificacion = '$codigoVerificacion'")->exists() &&
            Usuarios::find()->where('cod_verificacion is distinct from null')->exists();

        if ($correcto) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                // valida pero no llama al beforeSave y no genera el has (hay que corregirlo).
                $password = Yii::$app->security->generatePasswordHash($model->password);

                Yii::$app->db->createCommand("UPDATE usuarios SET password = :pass WHERE id = :id AND cod_verificacion = :codigo")
                    ->bindValue(':pass', $password)
                    ->bindValue(':id', $model->id)
                    ->bindValue(':codigo', $codigoVerificacion)
                    ->execute();

                //vaciar columna codigo_verificacion de la DB.
                Yii::$app->db->createCommand("UPDATE usuarios SET cod_verificacion = null WHERE id = :id AND cod_verificacion = :codigo")
                    ->bindValue(':id', $model->id)
                    ->bindValue(':codigo', $codigoVerificacion)
                    ->execute();

                Yii::$app->session->setFlash('success', 'Se ha modificado la contraseña correctamente.');
                return $this->goHome();
            }
        } else {
            Yii::$app->session->setFlash('error', 'No estás autorizado. Código de verificación no disponible.');
            return $this->goHome();
        }

        $model->password = '';
        $model->password_repeat = '';

        return $this->render('recuperar', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Usuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Usuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Usuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Usuarios::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
