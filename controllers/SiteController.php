<?php

namespace app\controllers;

use app\models\Carrito;
use app\models\Compras;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Generos;
use app\models\Juegos;
use app\models\Usuarios;
use yii\data\ActiveDataProvider;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout','checkout','make-payment'],
                'rules' => [
                    [
                        'actions' => ['logout','checkout','make-payment'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $usuarios = new ActiveDataProvider([
            'query' => Usuarios::find()->where('1=0'),
        ]);

        $juegos = new ActiveDataProvider([
            'query' => Juegos::find()->joinWith('genero g')->where('1=0'),
        ]);

        if (($cadena = Yii::$app->request->get('cadena', ''))) {
            $usuarios->query->where(['ilike', 'nombre', $cadena]);
            $juegos->query->where(['ilike', 'titulo', $cadena]);

            if ((Generos::find()->where(['ilike', 'denom', $cadena])->exists())) {
                $juegos->query->where(['ilike', 'g.denom', $cadena]);
            }
        }

        return $this->render('index', [
            'usuarios' => $usuarios,
            'juegos' => $juegos,
            'cadena' => $cadena,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            // cambiar el estado a conectado (1)
            Yii::$app->db->createCommand("UPDATE usuarios SET estado_id = 2 WHERE id = :usuario_id")
                ->bindValue(':usuario_id', $model->getUser()->getId())
                ->execute();
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        // cambiar el estado a desconectado (0) 
        Yii::$app->db->createCommand("UPDATE usuarios SET estado_id = 1 WHERE id = :usuario_id")
            ->bindValue(':usuario_id', Yii::$app->user->id)
            ->execute();

        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionCookie()
    {
        setcookie('aceptar', '1', time() + 3600 * 24 * 365, '/');

        $this->goBack();
    }

    /**
     * Lanza el proceso de checkout de PayPal.
     *
     * @param [] $juegos
     * @return void
     */
    public function actionCheckout($juegos)
    {
        // Setup order information array with all items
        $juegos = unserialize($juegos);
        $total = 0.00;
        $total = number_format((float) $total, 2, '.', '');
        $items = [];
        foreach ($juegos as $juego) {
            $model = Juegos::findOne($juego);
            $total += number_format((float) $model->precio, 2);
            $items[] = [
                'name' => $model->titulo,
                'price' => $model->precio,
                'quantity' => '1',
                'currency' => 'EUR',
            ];
        }
        $params = [
            'method' => 'paypal',
            'intent' => 'sale',
            'order' => [
                'description' => 'Descripción del pago',
                'subtotal' => $total,
                'shippingCost' => 0,
                'total' => $total,
                'currency' => 'EUR',
                'items' => $items,
            ]
        ];

        if (Yii::$app->PayPalRestApi->checkout($params)) {
            $_SESSION['params'] = [
                'order' => [
                    'description' => $params['order']['description'],
                    'subtotal' => $params['order']['subtotal'],
                    'shippingCost' => $params['order']['shippingCost'],
                    'total' => $params['order']['total'],
                    'currency' => $params['order']['currency'],
                ],
                'juegos' => $juegos,
            ];
        }
    }

    /**
     * Realiza el pago de Paypal y inserta datos (es decir, los juegos) que ha comprado
     * el usuario en la tabla de Compras. Si existen en el carrito, los borrará ya que han sido
     * comprados.
     *
     * @return void
     */
    public function actionMakePayment()
    {
        $params = [];
        $params = $_SESSION['params'];
        $juegos = $_SESSION['params']['juegos'];

        Yii::$app->PayPalRestApi->processPayment($params);
        $response = Yii::$app->response;

        if (!empty($response->data) && $response->data->state === 'approved') {
            foreach ($juegos as $juego) {
                $usuario_id = Yii::$app->user->id;
                $modelJuego = Juegos::findOne($juego);

                $estaEnCarrito = Carrito::find()->where(['usuario_id' => $usuario_id])
                    ->andFilterWhere(['juego_id' => $modelJuego->id])->exists();

                if ($estaEnCarrito) {
                    $modelCarrito = Carrito::find()->select('id')
                        ->where(['usuario_id' => $usuario_id])
                        ->andFilterWhere(['juego_id' => $modelJuego->id])->one();
                    $modelCarrito->delete();
                }

                $modelCompras = new Compras();
                $modelCompras->usuario_id = $usuario_id;
                $modelCompras->juego_id = $modelJuego->id;
                $modelCompras->subtotal = $modelJuego->precio;
                $modelCompras->total = $modelJuego->precio;

                $modelCompras->save();
            }
            unset($_SESSION['params']);
            unset($_SESSION['params']['juegos']);
            Yii::$app->session->setFlash('success', 'Pago realizado con éxito.');
            return $this->redirect(['juegos/tienda']);
        }
    }
}
