<?php

namespace app\controllers;

use app\models\Roles;
use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

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
                            $adminId = Roles::find()->select('id')->where(['=','rol','admin'])->scalar();

                            $usuario_rol_id = Usuarios::find()->select('rol_id')->where(['=','id',Yii::$app->user->id])->scalar();

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

            if ($this->actionCorreo($model->email, $mensaje)) {
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

    public function actionCorreo($email, $mensaje)
    {
        return Yii::$app->mailer->compose()
            ->setFrom(Yii::$app->params['smtpUsername'])
            ->setTo($email)
            ->setSubject('Confirmación de cuenta')
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
