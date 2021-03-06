<?php

namespace app\controllers;

use Yii;
use app\models\Chat;
use app\models\ChatSearch;
use app\models\Roles;
use app\models\Usuarios;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ChatController implements the CRUD actions for Chat model.
 */
class ChatController extends Controller
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
     * Lists all Chat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ChatSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Chat model.
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
     * Creates a new Chat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Chat();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Chat model.
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
     * Deletes an existing Chat model.
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
     * Acción que renderiza la vista en la que se ve los amigos del usuario,
     * el buscador de usuarios, etc.
     * @return void
     */
    public function actionPrincipal()
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }

        $logueado = Usuarios::findOne(Yii::$app->user->id);
        $usuarios = new ActiveDataProvider([
            'query' => Usuarios::find()->where('1=0'),
        ]);

        $arrayAmigos = $logueado->getAmigos();
        $dataProvider = new ArrayDataProvider([
            'key' => 'id',
            'allModels' => $arrayAmigos,
            'sort' => [
                'attributes' => ['id', 'nombre'],
            ],
            'pagination' => ['pageSize' => 10],
        ]);

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            $usuarios->query->where(['ilike', 'nombre', $cadena])
                ->andFilterWhere(['!=', 'id', Yii::$app->user->id]);

            if (filter_var($cadena, FILTER_VALIDATE_EMAIL)) {
                $usuarios->query->where(['ilike', 'email', $cadena])
                    ->andFilterWhere(['!=', 'id', Yii::$app->user->id]);
            }
        }


        return $this->render('principal', [
            'usuarios' => $usuarios,
            'cadena' => $cadena,
            'dataProvider' => $dataProvider,
            'logueado' => $logueado,
        ]);
    }

    /**
     * Inserta mensaje en la tabla de chat.
     *
     * @return void
     */
    public function actionInsertar()
    {
        if (isset($_POST['receptor_id']) && isset($_POST['mensaje'])) {
            $receptor_id = $_POST['receptor_id'];
            $mensaje = htmlspecialchars($_POST['mensaje'], ENT_QUOTES | ENT_SUBSTITUTE);

            if ($mensaje != '') {
                $model = new Chat();
                $model->emisor_id = Yii::$app->user->id;
                $model->receptor_id = $receptor_id;
                $model->mensaje = $mensaje;

                if ($model->save()) {
                    return $this->actionHistorial();
                }
            }
        }
    }

    /**
     * Acción que recoge el historial de chat.
     *
     * @return string
     */
    public function actionHistorial()
    {
        // version modificada
        $emisor_id = Yii::$app->user->id;
        $output = '';
        if (isset($_POST['receptor_id'])) {
            $receptor_id = (int) $_POST['receptor_id'];
            $res = Yii::$app->db->createCommand("SELECT * 
                FROM chat 
               WHERE (emisor_id = '" . $emisor_id . "' AND receptor_id = '" . $receptor_id . "')
               OR (emisor_id = '" . $receptor_id . "' AND receptor_id = '" . $emisor_id . "')
               ORDER BY created_at ASC")->queryAll();



            $output = '<ul class="list-unstyled">';

            foreach ($res as $fila) {
                $username = '';
                if ($fila['emisor_id'] == $emisor_id) {
                    $output .= '
                    <li class="mensajes darker">
                        <p> <b>Yo </b> - ' . $fila['mensaje'] . '
                        <div class="float-right">
                            <small><em>' .
                        date('H:i', strtotime($fila['created_at']))
                        . '</em></small>
                        </div>
                        </p>
                    </li>
                    ';
                } else {
                    $username = Usuarios::find()->select('nombre')->where(['=', 'id', $fila['emisor_id']])->scalar();
                    $output .= '
                <li class="recibidos">
                    <p>
                    ' . '<b>' . $username . ' - ' . '</b>'
                        . $fila['mensaje']
                        . '
                        <div class="float-right">
                            <small><em>' .
                        date('H:i', strtotime($fila['created_at']))
                        . '</em></small>
                        </div>
                    </p>
                </li>
                ';
                }
            }
            $output .= '</ul>';
        }
        return $output;
    }

    /**
     * Finds the Chat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Chat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Chat::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
