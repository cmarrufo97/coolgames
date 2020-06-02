<?php

use app\models\Juegos;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\widgets\ListView;

$this->title = 'Mi Biblioteca';
$this->params['breadcrumbs'][] = $this->title;


if ($tieneJuegos) {
    if ($dataProvider->totalCount > 0) {
?>

        <?= Html::beginForm(['usuarios/biblioteca'], 'get', [
            'class' => 'form-inline',
        ]) ?>

        <div class="form-group">
            <label for="buscar" class="sr-only">Buscar</label>
            <?= Html::textInput('buscar', $buscar, [
                'class' => 'form-control',
                'placeholder' => 'Buscar'
            ]) ?>
        </div>

        <?= Html::submitButton('Buscar', [
            'class' => 'btn btn-primary ml-2'
        ]) ?>
        <?= Html::endForm() ?>

        <div class="row justify-content-center">
            <?= ListView::widget([
                'dataProvider' => $dataProvider,
                'options' => ['class' => 'card-deck mt-4'],
                'layout' => '{items}',
                'summary' => '',
                'itemOptions' => [
                    'class' => 'card mt-2 bg-dark tarjeta',
                ],
                'itemView' => function ($model, $key, $index, $widget) {
                    $juego = Juegos::findOne($model->juego_id);
                    return $this->render('_biblioteca.php', [
                        'juego' => $juego,
                    ]);
                },
            ]) ?>
        </div>

        <div class="mt-2">
            <?= LinkPager::widget([
                'pagination' => $dataProvider->pagination,
            ]); ?>
        </div>
    <?php
    }
} else {
    ?>
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Vaya, no has comprado ningún juego aún.</h1>
            <?= Html::a('Dirígete a la tienda para comprar juegos', ['juegos/tienda'], [
                'class' => 'lead',
            ]) ?>
        </div>
    </div>
<?php
}
?>