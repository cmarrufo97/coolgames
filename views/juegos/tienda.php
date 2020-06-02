<?php

use app\models\Carrito;
use app\models\Compras;
use app\models\Valoraciones;
use kartik\rating\StarRating;
use yii\bootstrap4\Html;
use yii\bootstrap4\LinkPager;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\LinkSorter;
use yii\widgets\ListView;

$this->title = 'Tienda';
$this->params['breadcrumbs'][] = $this->title;
$valorar = Url::to(['valoraciones/valorar']);
$id = Yii::$app->user->id;

$js = <<<EOT
    $(document).ready(function () {
        $('.dropdown-menu li').addClass('dropdown-item');
    });
EOT;

$this->registerJs($js);
?>
<main>
    <?= Html::beginForm(['juegos/tienda'], 'get', [
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

    <div class="dropdown ml-4">
        <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Ordenar Por:
            <span class="caret"></span></button>
        <?= LinkSorter::widget([
            'sort' => $dataProvider->sort,
            'attributes' => $attributes,
            'options' => [
                'class' => 'linksorter-container dropdown-menu'
            ]
        ]) ?>
    </div>

    <?= Html::endForm() ?>

    <div class="row justify-content-center">
        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'options' => ['class' => 'card-deck mt-4 justify-content-center', 'tag' => 'section'],
            'layout' => '{items}',
            'summary' => '',
            'itemOptions' => [
                'class' => 'card bg-dark mt-2 tarjeta', 'tag' => 'article',
                'itemscope itemtype' => 'http://schema.org/Game/VideoGame',
            ],
            'itemView' => function ($model, $key, $index, $widget) {
                $modelValoracion = new Valoraciones();
                $modelCarrito = new Carrito();
                $id = Yii::$app->user->id;
                $estaComprado = Compras::find()->where(['usuario_id' => $id])
                    ->andFilterWhere(['juego_id' => $model->id])->exists();
                return $this->render('_juegos.php', [
                    'juego' => $model,
                    'modelValoracion' => $modelValoracion,
                    'modelCarrito' => $modelCarrito,
                    'id' => $id,
                    'estaComprado' => $estaComprado,
                ]);
            },
        ]) ?>
    </div>

    <div class="mt-2">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]); ?>
    </div>
</main>