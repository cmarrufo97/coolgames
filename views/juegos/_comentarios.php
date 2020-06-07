<?php

use app\models\Roles;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\helpers\Url;
$esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());
?>
<div>
    <div class="float-left mt-3 ml-3">
        <?= Html::img($usuario->getImagen(), ['class' => 'rounded img-fluid']) ?>
    </div>
    <!-- botones -->
    <div class="float-right">
        <div>
            <?php
            if ($comentario->usuario_id === Yii::$app->user->id || $esAdmin) {
            ?>
                <?=
                    Html::a(
                        '',
                        Url::to(['comentarios/delete', 'id' => $comentario->id]),
                        [
                            'class' => 'glyphicon glyphicon-trash text-decoration-none mt-2 mr-2',
                            'data-toggle' => 'tooltip',
                            'data-method' => 'POST',
                            'data-confirm' => '¿Está seguro de que desea eliminar este comentario?',
                            'title' => 'Eliminar',
                        ],
                    )
                ?>
            <?php
            }
            ?>
        </div>
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