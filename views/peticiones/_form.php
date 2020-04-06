<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Peticiones */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="peticiones-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'emisor_id')->textInput() ?>

    <?= $form->field($model, 'receptor_id')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
