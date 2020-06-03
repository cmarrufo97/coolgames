<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Juegos */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="descargar-form">

    <?php $form = ActiveForm::begin([
        'action' => ['juegos/descargar'],
    ]); ?>

    <?= $form->field($model, 'id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Descargar', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>