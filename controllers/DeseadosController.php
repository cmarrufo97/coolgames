<?php

namespace app\controllers;

use Yii;
use app\models\Deseados;
use app\models\DeseadosSearch;
use app\models\Roles;
use app\models\Usuarios;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeseadosController implements the CRUD actions for Deseados model.
 */
class DeseadosController extends Controller
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
     * Lists all Deseados models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DeseadosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Deseados model.
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
     * Creates a new Deseados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Deseados();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Deseados model.
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
     * Deletes an existing Deseados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['juegos/deseados']);
    }

    public function actionCrear($id = null)
    {
        if ($id === null && Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $model = new Deseados();
        $usuario_id = Yii::$app->user->id;
        $juego_id = $id;

        $model->usuario_id = $usuario_id;
        $model->juego_id = $juego_id;

        $existeDeseado = Deseados::find()->select('id')->where(['=', 'juego_id', $juego_id])
            ->exists();

        if (!$existeDeseado) {
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Juego añadido a deseados correctamente.');
                // return $this->redirect(['juegos/deseados', 'id' => $usuario_id]);
            }
        } else {
            Yii::$app->session->setFlash('warning', 'El juego ya existe en tu lista de deseados.');
        }
        return $this->redirect(['juegos/tienda']);
    }

    /**
     * Finds the Deseados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Deseados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Deseados::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
