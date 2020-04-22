<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Email de recuperación';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="site-resetear">
    <h1>Recuperar contraseña</h1>
    <p>Introduzca su email para recuperar su contraseña:</p>
    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-sm-4'],
        ],
    ]) ?>

    <?= $form->field($model, 'email')->textInput() ?>

    <div class="form-group">
        <div class="offset-sm-2">
            <?= Html::submitButton('Enviar', ['class' => 'btn btn-primary ml-1']) ?>
        </div>
    </div>


    <?php ActiveForm::end() ?>
</div>