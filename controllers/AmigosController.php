<?php

namespace app\controllers;

use Yii;
use app\models\Amigos;
use app\models\AmigosSearch;
use app\models\Peticiones;
use app\models\Roles;
use app\models\Usuarios;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AmigosController implements the CRUD actions for Amigos model.
 */
class AmigosController extends Controller
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
     * Lists all Amigos models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AmigosSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Amigos model.
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
     * Creates a new Amigos model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Amigos();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Amigos model.
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
     * Deletes an existing Amigos model.
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
     * Agrega a un usuario como amigo, al agregar a un usuario como amigo,
     * la petición se borra ya que se inserta los datos en la tabla de amigos.
     *
     * @param [type] $id
     * @return void
     */
    public function actionAgregar($id)
    {
        $amigo_id = $id;
        $model = new Amigos();
        $model->usuario_id = Yii::$app->user->id;
        $model->amigo_id = $amigo_id;
        $nombre = Usuarios::find()->select('nombre')->where(['=', 'id', $amigo_id])->scalar();

        if ($model->insert()) {
            Yii::$app->session->setFlash('success', "El usuario con nombre <b>$nombre</b> ha sido añadido como amigo");

            if (Peticiones::find()->where(['=', 'emisor_id', $model->amigo_id])
                ->andFilterWhere(['=', 'receptor_id', $model->usuario_id])
                ->exists()
            ) {
                // borrar la peticion de amistad ya que se aceptó anteriormente.abnf
                $peticion = Peticiones::find()->where(['=', 'emisor_id', $model->amigo_id])
                    ->andFilterWhere(['=', 'receptor_id', $model->usuario_id])->one();

                $peticion->delete();
            }
        }
        return $this->redirect(['chat/principal']);
    }

    /**
     * Acción para eliminar un usuario como amigo.
     *
     * @param [type] $id
     * @return void
     */
    public function actionEliminar($id)
    {
        $amigo_id = $id;
        $nombre = Usuarios::find()->select('nombre')->where(['=', 'id', $amigo_id])->scalar();

        $directa = Amigos::find()->where(['=', 'usuario_id', Yii::$app->user->id])
            ->andFilterWhere(['=', 'amigo_id', $amigo_id])
            ->exists();

        $inversa = Amigos::find()->where(['=', 'usuario_id', $amigo_id])
            ->andFilterWhere(['=', 'amigo_id', Yii::$app->user->id])
            ->exists();


        if ($directa) {
            $modelId = Amigos::find()->select('id')->where(['=', 'usuario_id', Yii::$app->user->id])
                ->andFilterWhere(['=', 'amigo_id', $amigo_id])
                ->scalar();
            $model = $this->findModel($modelId);

            if ($model->delete()) {
                Yii::$app->session->setFlash('success', "El usuario <b>$nombre</b> ha sido eliminado de tu lista de amigos correctamente");
            }
        }

        if ($inversa) {
            $modelId = Amigos::find()->select('id')->where(['=', 'usuario_id', $amigo_id])
                ->andFilterWhere(['=', 'amigo_id', Yii::$app->user->id])
                ->scalar();
            $model = $this->findModel($modelId);

            if ($model->delete()) {
                Yii::$app->session->setFlash('success', "El usuario <b>$nombre</b> ha sido eliminado de tu lista de amigos correctamente");
            }
        }


        return $this->redirect(['chat/principal']);
    }
    
    /**
     * Finds the Amigos model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Amigos the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Amigos::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
