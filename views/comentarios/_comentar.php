<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Comentarios */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-form">

    <?php $form = ActiveForm::begin([
        'id' => 'comentarForm',
        'action' => ['comentarios/comentar'],
    ]); ?>

    <?= $form->field($comentario, 'usuario_id')->hiddenInput([
        'value' => Yii::$app->user->id,
    ])->label(false) ?>

    <?= $form->field($comentario, 'juego_id')->hiddenInput([
        'value' => $model->id,
    ])->label(false) ?>

    <?php
    if (Yii::$app->user->isGuest) {
    ?>
        <?= $form->field($comentario, 'comentario')->textarea([
            'placeholder' => 'Tiene que estar logueado para dejar comentarios.',
            'class' => 'form-control textarea-juegos',
            'rows' => 4,
            'readonly' => true,
        ])->label(false) ?>


        <div class="form-group">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-primary disabled']) ?>
        </div>
    <?php
    } else {
    ?>
        <?= $form->field($comentario, 'comentario')->textarea([
            'placeholder' => 'Deja aquí tu comentario.',
            'class' => 'form-control textarea-juegos',
            'rows' => 4,
        ])->label(false) ?>


        <div class="form-group">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-primary']) ?>
        </div>

    <?php
    }
    ?>

    <?php ActiveForm::end(); ?>

</div>