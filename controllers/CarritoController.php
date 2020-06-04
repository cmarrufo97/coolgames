<?php

namespace app\controllers;

use Yii;
use app\models\Carrito;
use app\models\CarritoSearch;
use app\models\Compras;
use app\models\Juegos;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * CarritoController implements the CRUD actions for Carrito model.
 */
class CarritoController extends Controller
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
     * Lists all Carrito models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CarritoSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Carrito model.
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
     * Creates a new Carrito model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Carrito();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Carrito model.
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
     * Deletes an existing Carrito model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if ($this->findModel($id)->delete()) {
            Yii::$app->session->setFlash('success', 'Juego eliminado del carrito con éxito.');
        }

        return $this->redirect(['carrito/lista']);
    }

    /**
     * Acción para añadir un juego al carrito.
     *
     * @return void
     */
    public function actionCrear()
    {
        $model = new Carrito();

        $usuario_id = Yii::$app->request->post('Carrito')['usuario_id'];
        $juego_id = Yii::$app->request->post('Carrito')['juego_id'];

        $existe = Carrito::find()
            ->where(['=', 'usuario_id', $usuario_id])
            ->andFilterWhere(['=', 'juego_id', $juego_id])
            ->exists();

        $estaComprado = Compras::find()->where(['usuario_id' => $usuario_id])
            ->andFilterWhere(['juego_id' => $juego_id])->exists();

        if (!$estaComprado) {
            if (!$existe) {
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->session->setFlash('success', 'Juego añadido al carrito.');
                }
            } else {
                Yii::$app->session->setFlash('warning', 'Este juego ya ha sido añadido al carrito.');
            }
        } else {
            Yii::$app->session->setFlash('error', 'Ya has realizado la compra de este juego.');
        }

        return $this->redirect(['juegos/tienda', 'id' => $juego_id]);
    }

    /**
     * Muestra todos los juegos que hay en el carrito segun el id del usuario logueado.
     *
     * @param [type] $id
     * @return void
     */
    public function actionLista($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if ($id === null && !Yii::$app->user->isGuest) {
            $id = Yii::$app->user->id;
        }
        $usuario_id = $id;

        $juegos = Carrito::find()->select('juego_id')
            ->where(['=', 'usuario_id', $usuario_id])->all();

        $precioTotal = null;

        foreach ($juegos as $juego) {
            $precioTotal += (float) Juegos::find()->select('precio')
                ->where(['=', 'id', $juego->juego_id])->scalar();
        }


        $lista = new ActiveDataProvider([
            'query' => Carrito::find()->where(['=', 'usuario_id', $usuario_id]),
        ]);


        return $this->render('lista', [
            'lista' => $lista,
            'precioTotal' => $precioTotal,
            'juegos' => $juegos,
        ]);
    }

    /**
     * Finds the Carrito model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Carrito the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Carrito::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
