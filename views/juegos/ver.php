<?php

use app\models\Roles;
use app\models\Usuarios;
use yii\bootstrap4\Button;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = $this->title;
$usuario_id = Yii::$app->user->id;
$juego_id = $model->id;
?>

<div class="card bg-dark">
    <div class="card-body">
        <?= Html::img($model->getImagen(), [
            'class' => 'mx-auto d-block',
        ]) ?>
        <h1 class="lead text-center text-white">
            <?= $model->titulo ?>
        </h1>
    </div>
</div>

<h3 class="mt-2">Comentarios:</h3>

<?php
echo  $this->render('../comentarios/_comentar', [
    'comentario' => $comentario,
    'model' => $model,
]);
?>

<!-- Mostrar comentarios -->

<?php
$anterior = <<< EOT
<div class="card mt-2">
<div>
    <h5 class="card-title"><?= \$usuario->nombre ?></h5>
    <?= Html::img(\$usuario->getImagen(), ['class' => 'imagen-comentarios-usuarios']) ?>
</div>
<div class="card-body">
    <!-- <div>
        <h5 class="card-title"><?= \$usuario->nombre ?></h5>
        <?= Html::img(\$usuario->getImagen(), ['class' => 'imagen-comentarios-usuarios']) ?>
    </div> -->
    <div>
        <p class="card-text"><?= \$comentario->comentario ?></p>
    </div>
</div>
</div>

EOT;

foreach ($comentarios as $comentario) {
    $usuario = Usuarios::findOne(Usuarios::find()->select('id')->where(['=', 'id', $comentario->usuario_id])->scalar());
?>
    <div class="card mt-2 pb-4">
        <div>
            <div class="float-left mt-3 ml-3">
                <?= Html::img($usuario->getImagen(), ['class' => 'rounded img-fluid']) ?>
            </div>
            <!-- botones -->
            <div class="float-right">
                <div>
                    <?php
                    $esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());
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
    </div>

<?php
}
?>