<?php

namespace app\controllers;

use app\models\Deseados;
use app\models\Generos;
use Yii;
use app\models\Juegos;
use app\models\JuegosSearch;
use app\models\Roles;
use app\models\Usuarios;
use app\models\Valoraciones;
use yii\data\ActiveDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JuegosController implements the CRUD actions for Juegos model.
 */
class JuegosController extends Controller
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
     * Lists all Juegos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JuegosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'generos' => Generos::lista(),
        ]);
    }

    /**
     * Displays a single Juegos model.
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
     * Creates a new Juegos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Juegos();


        if ($model->load(Yii::$app->request->post())) {
            $model->uploadImage();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
            'generos' => Generos::lista(),
        ]);
    }

    /**
     * Updates an existing Juegos model.
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
            'generos' => Generos::lista(),
        ]);
    }

    /**
     * Deletes an existing Juegos model.
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

    public function actionTienda()
    {
        // if (Yii::$app->user->isGuest) {
        //     return $this->redirect(['site/login']);
        // }
        
        $model = new Valoraciones();


        return $this->render('tienda', [
            'juegos' => Juegos::lista(),
            'model' => $model,
        ]);
    }

    public function actionDeseados($id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        if (!Yii::$app->user->isGuest) {
            $id = Yii::$app->user->id;
        }
        $usuario_id = $id;

        $deseados = new ActiveDataProvider([
            'query' => Deseados::find()->where(['=', 'usuario_id', $usuario_id]),
        ]);

        return $this->render('deseados', [
            'deseados' => $deseados,
        ]);
    }

    /**
     * Finds the Juegos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Juegos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Juegos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
