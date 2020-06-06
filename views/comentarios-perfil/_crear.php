<?php

use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-perfil-form">

    <?php $form = ActiveForm::begin([
        'action' => ['comentarios-perfil/crear'],
    ]); ?>

    <?= $form->field($model, 'emisor_id')->hiddenInput([
        'value' => $emisor_id,
    ])->label(false) ?>

    <?= $form->field($model, 'receptor_id')->hiddenInput([
        'value' => $receptor_id,
    ])->label(false) ?>

    <?php
    if (Yii::$app->user->isGuest) {
    ?>
        <?= $form->field($model, 'comentario')->textarea([
            'rows' => 4,
            'placeholder' => "Tienes que estar logueado para comentar.",
            'readonly' => true,
        ])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-primary disabled']) ?>
        </div>
    <?php
    } else {
    ?>
        <?= $form->field($model, 'comentario')->textarea([
            'rows' => 4,
            'placeholder' => "Deja un comentario aquí."
        ])->label(false) ?>
        <div class="form-group">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    <?php
    }
    ?>

    <?php ActiveForm::end(); ?>

</div>