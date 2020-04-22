<?php

use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Recuperar contraseña';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="usuarios-recuperar">
    <h1>Recuperar contraseña</h1>
    <p>Introduzca su nueva contraseña y confirme:</p>

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-sm-5'],
        ],
    ]) ?>

    <?= $form->field($model, 'password')->passwordInput()->label('Nueva contraseña') ?>
    <?= $form->field($model, 'password_repeat')->passwordInput() ?>


    <div class="form-group">
        <div class="offset-sm-2">
            <?= Html::submitButton('Modificar', ['class' => 'btn btn-primary ml-1', 'name' => 'login-button']) ?>
        </div>
    </div>



    <?php ActiveForm::end() ?>
</div>