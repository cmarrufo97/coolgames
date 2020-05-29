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

<div class="dropdown">
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
<main>
    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'options' => ['class' => 'card-deck mt-4', 'tag' => 'section'],
        'layout' => '{items}',
        'summary' => '',
        'itemOptions' => ['class' => 'card bg-dark', 'tag' => 'article'],
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

    <div class="mt-2">
        <?= LinkPager::widget([
            'pagination' => $dataProvider->pagination,
        ]); ?>
    </div>
</main>