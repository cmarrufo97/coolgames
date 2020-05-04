<?php

use app\models\Valoraciones;
use kartik\rating\StarRating;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = 'Tienda';
$this->params['breadcrumbs'][] = $this->title;
$valorar = Url::to(['valoraciones/valorar']);
$id = Yii::$app->user->id;

$js = <<<EOT
    $(document).ready(function () {
        $.ajax({
            url: '$valorar',
            method: 'GET',
            data: {juego_id: 1, estrellas: 5},
            success: function (data) {
                console.log(data);
            },
        });
    });
EOT;


foreach ($juegos as $juego) {
?>
    <div class="card-deck mt-5">
        <div class="card bg-dark">
            <div class="text-center mt-4">
                <?= Html::img($juego->getImagen()) ?>
            </div>
            <div class="card-body bg-white mt-4">
                <h5 class="card-title">
                    <?= Html::a(
                        $juego->titulo,
                        Url::to(['juegos/view', 'id' => $juego->id])
                    )
                    ?>
                </h5>
                <div class="bg-white">
                    <?php
                    $valoracion = Valoraciones::find()
                        ->select('estrellas')
                        ->where(['=', 'usuario_id', $id])
                        ->andFilterWhere(['=', 'juego_id', $juego->id])
                        ->scalar();
                    ?>
                    <!-- Valoracion -->
                    <?=
                        StarRating::widget([
                            'name' => 'rating_19',
                            'value' => $valoracion,
                            'pluginOptions' => [
                                'stars' => 5,
                                'min' => 0,
                                'max' => 5,
                                'step' => 0.5,
                                'filledStar' => '<i class="glyphicon glyphicon-star"></i>',
                                'emptyStar' => '<i class="glyphicon glyphicon-star-empty"></i>',
                                'defaultCaption' => '{rating} estrellas',
                                'starCaptions' => new JsExpression("function(val){return val == 1 ? 'Una estrella' : val + ' estrellas';}")
                            ],
                            'pluginEvents' => [
                                'rating:change' => "function (event,value,caption) {
                                    $.ajax('$valorar',{
                                        type: 'POST',
                                        data: { id: $id, juego_id: $juego->id, estrellas: value},
                                        success: function (data) {
                                            console.log(data);
                                        },
                                    });
                                }",
                            ],
                        ]);
                    ?>
                    <!-- Valoración Global -->
                    <p class="lead">Valoración Global/General:</p>
                    <?php
                    echo StarRating::widget([
                        'name' => 'rating_19',
                        'value' => $juego->getValoracionGlobal(),
                        'pluginOptions' => [
                            'displayOnly' => true,
                            'stars' => 5,
                            'min' => 0,
                            'max' => 5,
                            'step' => 0.1,
                            'filledStar' => '<i class="glyphicon glyphicon-star"></i>',
                            'emptyStar' => '<i class="glyphicon glyphicon-star-empty"></i>',
                            'defaultCaption' => '{rating} estrellas',
                            'starCaptions' => new JsExpression("function(val){return val == 1 ? 'Una estrella' : val + ' estrellas';}")
                        ],
                        'pluginEvents' => [
                            'rating:change' => "function (event,value,caption) {
                                    $.ajax('$valorar',{
                                        type: 'POST',
                                        data: { id: $id, juego_id: $juego->id, estrellas: value},
                                        success: function (data) {
                                            console.log(data);
                                        },
                                    });
                                }",
                        ],
                    ]);
                    ?>
                </div>

            </div>
            <div class="card-footer bg-white">
                <?= Html::a('Añadir a deseados', Url::to(['deseados/crear', 'id' => $juego->id]), [
                    'class' => 'btn btn-sm btn-primary'
                ]) ?>
                <button class="btn btn-sm btn-info">Añadir al carrito</button>
                <button class="btn btn-sm btn-success btn-comprar">Comprar</button>
            </div>
        </div>
    </div>
<?php
}
?>