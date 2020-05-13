<?php

use app\models\ComentariosPerfil;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\bootstrap4\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */
/* @var $form yii\bootstrap4\ActiveForm */

$comentario = ComentariosPerfil::findOne($padre_id);
$usuario = Usuarios::findOne($comentario->emisor_id);
?>

<h1>Responder al siguiente comentario: </h1>

<div class="card mt-2 pb-4">
    <div>
        <div class="float-left mt-3 ml-3">
            <?= Html::img($usuario->getImagen(), ['class' => 'rounded img-fluid']) ?>
        </div>
        <div class="float-left meta">
            <div class="title h5 mt-3 ml-3">
                <a href="#"><b><?= $usuario->nombre ?></b></a>
            </div>
            <h6 class="text-muted time ml-3">
                <?=
                    date('m-d-Y H:i', strtotime($comentario->created_at));
                ?>
            </h6>
            <div class="mt-4 ml-3">
                <p><?= $comentario->comentario ?></p>
            </div>
        </div>
    </div>
</div>


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