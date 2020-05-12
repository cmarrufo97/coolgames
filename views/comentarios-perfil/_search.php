<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfilSearch */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="comentarios-perfil-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'emisor_id') ?>

    <?= $form->field($model, 'receptor_id') ?>

    <?= $form->field($model, 'comentario') ?>

    <?= $form->field($model, 'edited_at') ?>

    <?php // echo $form->field($model, 'padre_id') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
