<?php

use app\models\ComentariosPerfil;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */
/* @var $form yii\bootstrap4\ActiveForm */

// $comentario = ComentariosPerfil::findOne($padre_id);
// $usuarioId = $comentario->emisor_id;
$comentario = ComentariosPerfil::findOne($padre_id);
// $usuario = Usuarios::findOne(ComentariosPerfil::findOne($padre_id)->emisor_id);
?>

<h1>Responder al siguiente comentario: </h1>


<div class="comentarios-perfil-form">

    <?php $form = ActiveForm::begin([
        'id' => 'responderForm',
        'action' => ['comentarios-perfil/responder'],
    ]); ?>

    <?= $form->field($model, 'emisor_id')->hiddenInput([
        'value' => Yii::$app->user->id,
    ])->label(false) ?>

    <?= $form->field($model, 'receptor_id')->hiddenInput([
        'value' => $receptor_id,
    ])->label(false) ?>


    <?= $form->field($model, 'padre_id')->hiddenInput([
        'value' => $padre_id,
    ])->label(false) ?>

    <div class="ml-3">
        <?= $form->field($model, 'comentario')->textarea([
            'rows' => 4,
        ])->label(false) ?>

        <div class="form-group">
            <?= Html::submitButton('Responder', ['class' => 'btn btn-sm btn-primary']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>