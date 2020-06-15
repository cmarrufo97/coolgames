<?php

use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */
/* @var $form yii\bootstrap4\ActiveForm */

$id = Yii::$app->request->post('id');
?>

<h1>Editar un comentario:</h1>

<div class="comentarios-perfil-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'emisor_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'receptor_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'comentario')->textarea(['rows' => 4]) ?>

    <?= $form->field($model, 'edited_at')->hiddenInput([
        'value' => date('Y-m-d H:i:s', time()),
    ])->label(false) ?>

    <?= $form->field($model, 'padre_id')->hiddenInput()->label(false) ?>

    <div class="form-group">
        <?= Html::submitButton('Editar', [
            'class' => 'btn btn-primary',
            'data' => [
                'method' => 'POST',
                'params' => ['id' => $id],
            ],
        ]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>