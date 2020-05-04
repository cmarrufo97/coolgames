<?php

namespace app\controllers;

use Yii;
use app\models\Valoraciones;
use app\models\ValoracionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;

/**
 * ValoracionesController implements the CRUD actions for Valoraciones model.
 */
class ValoracionesController extends Controller
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
        ];
    }

    /**
     * Lists all Valoraciones models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ValoracionesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Valoraciones model.
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
     * Creates a new Valoraciones model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Valoraciones();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Valoraciones model.
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
     * Deletes an existing Valoraciones model.
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

    public function actionValorar()
    {


        $existeValoracionPrevia = Valoraciones::find()
            ->where(['=', 'usuario_id', $_POST['id']])
            ->andFilterWhere(['=', 'juego_id', $_POST['juego_id']])
            ->exists();

        if ($existeValoracionPrevia) {
            $id = Valoraciones::find()
                ->select('id')
                ->where(['=', 'usuario_id', $_POST['id']])
                ->andFilterWhere(['=', 'juego_id', $_POST['juego_id']])
                ->scalar();

            $model = $this->findModel($id);
            $model->estrellas = $_POST['estrellas'];
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Valoración enviada con éxito.');
            }
        } else {
            $model = new Valoraciones();
            $model->usuario_id = $_POST['id'];
            $model->juego_id = $_POST['juego_id'];
            $model->estrellas = $_POST['estrellas'];
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Valoración enviada con éxito.');
            }
        }

        return $this->redirect(['juegos/tienda']);
    }

    /**
     * Finds the Valoraciones model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Valoraciones the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Valoraciones::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}