<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Juegos */
/* @var $form yii\bootstrap4\ActiveForm */
?>

<div class="juegos-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'genero_id')->dropDownList($generos)->label('Género') ?>

    <?= $form->field($model, 'flanzamiento')->textInput() ?>

    <?= $form->field($model, 'precio')->textInput() ?>

    <?= $form->field($model, 'imgUpload')->fileInput() ?>

    <!-- <?= $form->field($model, 'imagen')->textInput(['maxlength' => true]) ?> -->

    <!-- <?= $form->field($model, 'created_at')->textInput() ?> -->

    <div class="form-group">
        <?= Html::submitButton('Crear', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>