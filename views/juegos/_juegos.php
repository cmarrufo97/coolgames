<?php

use app\models\Comentarios;
use app\models\Generos;
use app\models\Valoraciones;
use kartik\rating\StarRating;
use yii\bootstrap4\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

?>

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
</div>

<div class="bg-white">
    <div class="text-center">
        <div>
            <?php
            if (!Yii::$app->user->isGuest) {
            ?>
                <p>Tu Valoración:</p>
            <?php
            }
            ?>
            <?php
            $miValoracion = Valoraciones::find()
                ->select('estrellas')
                ->where(['=', 'usuario_id', $id])
                ->andFilterWhere(['=', 'juego_id', $juego->id])
                ->scalar();
            ?>
            <?=
                $this->render('../valoraciones/_valorar', [
                    'valoracion' => $modelValoracion,
                    'juego_id' => $juego->id,
                    'votos' => $miValoracion,
                ]);
            ?>
        </div>
        <!-- Valoración Global -->
        <p>Valoración Global/General:</p>
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
                'showCaption' => false,
                'filledStar' => '<i class="glyphicon glyphicon-star"></i>',
                'emptyStar' => '<i class="glyphicon glyphicon-star-empty"></i>',
                'defaultCaption' => '{rating} estrellas',
                'starCaptions' => new JsExpression("function(val){return val == 1 ? 'Una estrella' : val + ' estrellas';}")
            ],
        ]);
        ?>
        <div>
            <h5>Género:
                <span itemprop="genre">
                    <?= Generos::find()->select('denom')->where(['id' => $juego->genero_id])->scalar(); ?>
                </span>
            </h5>
        </div>

        <div>
            <h5>Precio: <?= Yii::$app->formatter->asCurrency($juego->precio) ?></h5>
        </div>
    </div>
</div>
<div class="card-footer bg-white">
    <?= Html::a('Añadir a deseados', Url::to(['deseados/crear', 'id' => $juego->id]), [
        'class' => 'btn btn-sm btn-primary form-control'
    ]) ?>
    <!-- <button class="btn btn-sm btn-info btn-comprar">Añadir al carrito</button> -->

    <?=
        $this->render('../carrito/_crear', [
            'model' => $modelCarrito,
            'juego_id' => $juego->id,
        ]);
    ?>

    <?php
    $countComentarios = Comentarios::find()->where(['juego_id' => $juego->id])->count('comentario');

    if ($countComentarios > 0) {
        echo Html::a("Opiniones ($countComentarios)", Url::to(['juegos/ver', 'id' => $juego->id]), [
            'class' => 'btn btn-sm btn-secondary btn-comprar form-control'
        ]);
    } else {
        echo Html::a("Opiniones", Url::to(['juegos/ver', 'id' => $juego->id]), [
            'class' => 'btn btn-sm btn-secondary btn-comprar form-control'
        ]);
    }
    ?>

    <div class="text-center mt-2">
        <?php
        if ($estaComprado) {
            echo Html::a('¡Lo tengo!', ['usuarios/biblioteca'], [
                'class' => 'text-success d-inline'
            ]);
        } else {
            $juegos = [];
            $juegos['juegos'] = [$juego->id];
            echo Html::a('Comprar', [
                'site/checkout',
                'juegos' => serialize($juegos),
            ], [
                'class' => 'btn btn-sm btn-success btn-comprar form-control mt-2'
            ]);
        }
        ?>
    </div>
</div>