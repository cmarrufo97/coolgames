<?php

use app\models\Comentarios;
use app\models\Roles;
use app\models\Usuarios;
use yii\bootstrap4\Button;
use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\helpers\Url;
use yii\widgets\ListView;

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = $this->title;
$usuario_id = Yii::$app->user->id;
$juego_id = $model->id;
?>

<div itemscope itemtype="http://schema.org/Game/VideoGame" class="card bg-dark">
    <div class="card-body">
        <?= Html::img($model->getImagen(), [
            'alt' => $model->titulo,
            'class' => 'mx-auto d-block',
            'itemprop' => 'image',
        ]) ?>
        <h1 class="lead text-center text-white">
            <span itemprop="name">
                <?= $model->titulo ?>
            </span>
        </h1>
    </div>
</div>

<h3 class="mt-2">Comentarios:</h3>

<?php
echo  $this->render('../comentarios/_comentar', [
    'comentario' => $comentario,
    'model' => $model,
]);
?>

<?= ListView::widget([
    'dataProvider' => $dataProvider,
    'layout' => '{items}',
    'summary' => '',
    'itemOptions' => [
        'class' => 'card mt-2 pb-4',
        'itemprop' => 'comment'
    ],
    'itemView' => function ($model, $key, $index, $widget) {
        $usuario = Usuarios::findOne($model->usuario_id);
        return $this->render('_comentarios.php', [
            'comentario' => $model,
            'usuario' => $usuario,
        ]);
    },
])
?>

<div class="mt-2">
    <?= LinkPager::widget([
        'pagination' => $dataProvider->pagination,
    ]); ?>
</div>