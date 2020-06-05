<?php

namespace app\controllers;

use Yii;
use app\models\Peticiones;
use app\models\PeticionesSearch;
use app\models\Roles;
use app\models\Usuarios;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PeticionesController implements the CRUD actions for Peticiones model.
 */
class PeticionesController extends Controller
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
                'only' => ['index', 'create', 'update'],
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
     * Lists all Peticiones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PeticionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Peticiones model.
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
     * Creates a new Peticiones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Peticiones();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Peticiones model.
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

    /**
     * Deletes an existing Peticiones model.
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
     * Acción que permite crear una peticion de amistad de un usuario a otro.
     *
     * @param [type] $receptor_id
     * @return void
     */
    public function actionCrear($receptor_id)
    {
        $model = new Peticiones();
        $model->emisor_id = Yii::$app->user->id;
        $model->receptor_id = $receptor_id;

        $nombreAmigo = Usuarios::find()->select('nombre')->where(['=', 'id', $receptor_id])->scalar();

        $existePeticion = Peticiones::find()->where(['=', 'emisor_id', $model->emisor_id])
            ->andFilterWhere(['=', 'receptor_id', $model->receptor_id])
            ->exists();

        if (!$existePeticion) {
            if ($model->insert()) {
                Yii::$app->session->setFlash('success', "Petición de amistad enviada a <b>$nombreAmigo</b> correctamente");
            } else {
                Yii::$app->session->setFlash('error', "Hubo un fallo al enviar la petición de amistad a <b>$nombreAmigo</b>");
            }
        } else {
            Yii::$app->session->setFlash('warning', "Ya hay en marcha una petición de amistad para <b>$nombreAmigo</b>");
        }

        return $this->redirect(['chat/principal']);
    }

    /**
     * Acción que rechaza una peticion de un usuario, al rechazarla se borra la petición en la
     * base de datos.
     *
     * @param [type] $emisor_id
     * @return void
     */
    public function actionRechazar($emisor_id)
    {
        $receptor_id = Yii::$app->user->id;

        $modelId = Peticiones::find()->select('id')->where(['=', 'emisor_id', $emisor_id])
            ->andFilterWhere(['=', 'receptor_id', $receptor_id]);

        $model = $this->findModel($modelId);

        if ($model->delete()) {
            Yii::$app->session->setFlash('success', 'Solicitud de amistad rechazada correctamente.');
        }
        return $this->redirect(['chat/principal']);
    }

    /**
     * Finds the Peticiones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Peticiones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Peticiones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
