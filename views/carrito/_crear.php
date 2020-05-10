<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Carrito */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div>

    <?php $form = ActiveForm::begin([
        'action' => ['carrito/crear'],
    ]); ?>

    <?= $form->field($model, 'usuario_id')->hiddenInput([
        'id' => Yii::$app->security->generateRandomString(),
        'value' => Yii::$app->user->id,
    ])->label(false) ?>

    <?= $form->field($model, 'juego_id')->hiddenInput([
        'id' => Yii::$app->security->generateRandomString(),
        'value' => $juego_id,
    ])->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Añadir al carrito', ['class' => 'btn btn-sm btn-info form-control']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>