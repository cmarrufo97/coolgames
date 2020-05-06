<?php

use app\models\Valoraciones;
use kartik\rating\StarRating;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Valoraciones */
/* @var $form yii\bootstrap4\ActiveForm */

$valorar = Url::to(['valoraciones/valorar']);
$id = Yii::$app->user->id;

$miValoracion = Valoraciones::find()
    ->select('estrellas')
    ->where(['=', 'usuario_id', $id])
    ->andFilterWhere(['=', 'juego_id', $juego_id])
    ->scalar();
?>

<div class="valoraciones-form">

    <?php $form = ActiveForm::begin([
        'id' => 'valorarForm',
        'action' => ['valoraciones/valorar'],
    ]); ?>

    <?= $form->field($valoracion, 'usuario_id')->hiddenInput([
        'value' => Yii::$app->user->id,
    ])->label(false) ?>

    <?= $form->field($valoracion, 'juego_id')->hiddenInput([
        'value' => $juego_id,
    ])->label(false) ?>



    <?php
    if (!Yii::$app->user->isGuest) {

        if ($miValoracion != false) {
            echo $form->field($valoracion, 'estrellas')->widget(StarRating::class, [
                'name' => 'rating_19',
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
                        $('#enviar-votacion').trigger('click');
                    }",
                ],
            ])->label(false)->hiddenInput([
                'value' => $miValoracion,
            ]);
        } else {
            echo $form->field($valoracion, 'estrellas')->widget(StarRating::class, [
                'name' => 'rating_19',
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
                        $('#enviar-votacion').trigger('click');
                    }",
                ],
            ])->label(false);
        }
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-success d-none', 'id' => 'enviar-votacion']) ?>
    </div>


    <?php ActiveForm::end(); ?>

</div>