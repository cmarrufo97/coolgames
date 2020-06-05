<?php

namespace app\controllers;

use Yii;
use app\models\ComentariosPerfil;
use app\models\ComentariosPerfilSearch;
use app\models\Roles;
use app\models\Usuarios;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;

/**
 * ComentariosPerfilController implements the CRUD actions for ComentariosPerfil model.
 */
class ComentariosPerfilController extends Controller
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
     * Lists all ComentariosPerfil models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ComentariosPerfilSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ComentariosPerfil model.
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
     * Creates a new ComentariosPerfil model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ComentariosPerfil();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ComentariosPerfil model.
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
     * Deletes an existing ComentariosPerfil model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());

        if (
            $model->emisor_id == Yii::$app->user->id
            || $model->receptor_id == Yii::$app->user->id || $esAdmin
        ) {
            if ($model->delete()) {
                Yii::$app->session->setFlash('success', 'Comentario borrado con éxito.');
            } else {
                Yii::debug($model->getErrors());
            }
        }

        return $this->redirect(['usuarios/perfil', 'id' => $model->receptor_id]);
    }

    /**
     * Añade un comentario en el perfil de un usuario a otro.
     *
     * @return void
     */
    public function actionCrear()
    {
        $model = new ComentariosPerfil();
        $receptor_id = Yii::$app->request->post('ComentariosPerfil')['receptor_id'];
        // $nombreReceptor = Usuarios::findOne($receptor_id)->nombre;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', "Comentario enviado con éxito.");
        }

        return $this->redirect(['usuarios/perfil', 'id' => $receptor_id]);
    }

    /**
     * Acción que renderiza una vista para responder a un comentario concreto de
     * un usuario.
     * @return void
     */
    public function actionResponder()
    {
        $model = new ComentariosPerfil();
        $receptor_id = Yii::$app->request->get('receptor_id');
        $padre_id = Yii::$app->request->get('padre_id');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Comentario respondido con éxito.');
            return $this->redirect(['usuarios/perfil', 'id' => $model->receptor_id]);
        }


        return $this->render('_responder', [
            'model' => $model,
            'receptor_id' => $receptor_id,
            'padre_id' => $padre_id,
        ]);
    }

    /**
     * Acción que renderiza una vista para editar un comentario de perfil concreto,
     * siempre y cuando el comentario pertenezca al autor del mismo.
     *
     * @return void
     */
    public function actionEditar()
    {
        $id = Yii::$app->request->get('id');
        $model = $this->findModel($id);

        if ($model->emisor_id === Yii::$app->user->id) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', 'Comentario editado con éxito.');
                return $this->redirect(['usuarios/perfil', 'id' => $model->receptor_id]);
            }
        }else {
            throw new ForbiddenHttpException('No tienes permisos para realizar esa acción.');
        }

        return $this->render('_editar', [
            'model' => $model,
        ]);
    }

    /**
     * Finds the ComentariosPerfil model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ComentariosPerfil the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ComentariosPerfil::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
