<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Coolgames';

$this->registerJsFile(
    '@web/js/bootbox.min.js',
    ['depends' => [\yii\web\JqueryAsset::class]]
);

$cookie = Url::to(['site/cookie']);

$js = <<<EOT
    $(document).ready(function() {
        bootbox.confirm({
            title: "Política de Cookies",
            message: "Este sitio web utiliza cookies para recoger datos de los usuarios con el fin de mejorar la experiencia.",
            buttons: {
                confirm: {
                    label: 'Aceptar',
                    className: 'btn-primary'
                },
                cancel: {
                    label: 'Rechazar',
                    className: 'btn-secondary',
                },
            },
            callback: function (result) {
                if (result) {
                    window.location = "$cookie";
                }else {
                    window.location = "http://www.google.es";
                }
            }
        });
    });
EOT;

$css = <<<EOT
@keyframes animacion {
    0% {

        transform: translateY(700px);
    }
    100% {
        transform: translateY(0);
    }
}

.modal-dialog {
    animation-name: animacion;
    -webkit-animation: animacion;
    -moz-animation: animacion;
    -o-animation: animacion;
    animation-duration: 1s;
    transition-property: all;
    transition-duration: 2s;
    transition-timing-function: linear;
}
EOT;

$this->registerCss($css);

if (!isset($_COOKIE['aceptar'])) {
    $this->registerJs($js);
}

?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Busque lo que quiera</h1>
        <p class="lead">Busque usuarios y juegos por título o género </p>

        <div>
            <?= Html::beginForm(['site/index'], 'get') ?>
            <div class="form-group">
                <?= Html::textInput('cadena', $cadena, ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="body-content">
        <?php if ($usuarios->totalCount > 0) : ?>
            <h3>Usuarios</h3>
            <div class="row">
                <?= GridView::widget([
                    'dataProvider' => $usuarios,
                    'columns' => [
                        'nombre',
                        [
                            'class' => ActionColumn::class,
                            'controller' => 'usuarios',
                            'template' => '{view}',
                        ],
                    ],
                ]) ?>
            </div>
        <?php endif ?>

        <?php if ($juegos->totalCount > 0) : ?>
            <h3>Juegos</h3>
            <div class="row">
                <?= GridView::widget([
                    'dataProvider' => $juegos,
                    'columns' => [
                        'titulo',
                        [
                            'class' => ActionColumn::class,
                            'controller' => 'juegos',
                            'template' => '{view}',
                        ],
                    ],
                ]) ?>
            </div>
        <?php endif ?>



    </div>
</div>