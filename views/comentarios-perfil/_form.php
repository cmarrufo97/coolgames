<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'emisor_id')->textInput() ?>

    <?= $form->field($model, 'receptor_id')->textInput() ?>

    <?= $form->field($model, 'comentario')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'edited_at')->textInput() ?>

    <?= $form->field($model, 'padre_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
