<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\widgets\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use app\assets\AppAsset;
use app\models\Carrito;
use app\models\Roles;
use app\models\Usuarios;
use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>

    <?php 
        function getcountCarrito() {
            if (!Yii::$app->user->isGuest) {
                $usuario = Usuarios::findOne(Yii::$app->user->id);
                if ($usuario->getItemsCarrito() > 0) {
                    return ' ('.$usuario->getItemsCarrito() . ')'; 
                }
            }
            // return '';
        }
    ?>

    <div class="wrap">
        <?php
        NavBar::begin([
            'brandLabel' => 'CoolGames',
            'brandUrl' => Yii::$app->homeUrl,
            'options' => [
                'class' => 'navbar-dark bg-dark navbar-expand-md fixed-top',
            ],
            'collapseOptions' => [
                'class' => 'justify-content-end',
            ],
        ]);
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav'],
            'items' => [
                // ['label' => 'Home', 'url' => ['/site/index']],
                [
                    'label' => 'Mi Biblioteca', 'url' => ['/usuarios/biblioteca'],
                    'visible' => !Yii::$app->user->isGuest
                ],
                ['label' => 'Tienda', 'url' => ['/juegos/tienda']],
                // ['label' => 'Deseados', 'url' => ['/juegos/deseados']],
                ['label' => 'Carrito' . getcountCarrito(), 'url' => ['/carrito/lista']],
                // . '('..')'
                ['label' => 'Social', 'url' => ['/chat/principal']],
                [
                    'label' => 'Mi Perfil', 'url' => ['/usuarios/perfil', 'id' => Yii::$app->user->id],
                    'visible' => !Yii::$app->user->isGuest
                ],
                // Visible solo para los Admins
                [
                    'label' => 'Usuarios', 'url' => ['/usuarios/index'],
                    'visible' => Usuarios::find()->select('rol_id')->where(['id' => Yii::$app->user->id])->scalar() === Roles::find()->select('id')->where(['rol' => 'admin'])->scalar(),
                ],
                [
                    'label' => 'Generos', 'url' => ['/generos/index'],
                    'visible' => Usuarios::find()->select('rol_id')->where(['id' => Yii::$app->user->id])->scalar() === Roles::find()->select('id')->where(['rol' => 'admin'])->scalar(),
                ],
                [
                    'label' => 'Juegos', 'url' => ['/juegos/index'],
                    'visible' => Usuarios::find()->select('rol_id')->where(['id' => Yii::$app->user->id])->scalar() === Roles::find()->select('id')->where(['rol' => 'admin'])->scalar(),
                ],
                [
                    'label' => 'Acceso',
                    'items' => [
                        !Yii::$app->user->isGuest ? ['label' => 'Deseados', 'url' => ['/juegos/deseados']] : '',
                        !Yii::$app->user->isGuest ?
                            (Html::beginForm(['/site/logout'], 'post')
                                . Html::submitButton(
                                    'Logout (' . Yii::$app->user->identity->nombre . ')',
                                    ['class' => 'dropdown-item'],
                                )
                                . Html::endForm()) : (Html::beginForm() . Html::a('Login', ['/site/login'], ['class' => 'dropdown-item']) . Html::a('Registrarse',
                                ['/usuarios/registrar'], ['class' => 'dropdown-item']) . Html::endForm()),
                    ],
                ],
            ],
        ]);
        NavBar::end();
        ?>

        <div class="container">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <?= $content ?>
        </div>
    </div>

    <footer class="footer">
        <div class="container">
            <p class="float-left">&copy; CoolGames <?= date('Y') ?></p>

            <p class="float-right">Desarollado por Christian Marrufo Rodríguez</p>
        </div>
    </footer>

    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>