<?php

use app\models\Roles;
use app\models\Usuarios;
use app\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Mi Perfil';
$this->params['breadcrumbs'][] = $this->title;
?>

<!-- Codigo viejo -->
<!-- 

<div class="usuarios-view">
    <div class="row">Eliminar
        <div class="col">
            <?= Html::img(
                $model->getImagen(),
                ['alt' => 'Imagen de perfil', 'class' => 'img-fluid rounded']
            )
            ?>
            <h1><?= $model->login ?></h1>


        </div>
    </div>
    <div class="row">
        <div class="col mt-2">
            <?= Html::a(
                'Modificar foto',
                ['usuarios/imagen', 'id' => Yii::$app->user->id],
                [
                    'class' => 'btn btn-primary'
                ]
            ) ?>
        </div>
    </div>
</div> -->
<!-- Fin codigo viejo -->

<!-- Codigo nuevo -->
<div class="usuarios-perfil">
    <div class="container">
        <div class="row my-2">
            <div class="col-lg-8 order-2 mt-2">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="" data-target="#profile" data-toggle="tab" class="nav-link active">Perfil</a>
                    </li>
                    <li class="nav-item">
                        <a href="" data-target="#comentarios" data-toggle="tab" class="nav-link">Comentarios</a>
                    </li>
                    <?php
                    if ($model->id == Yii::$app->user->id) {
                    ?>
                        <li class="nav-item">
                            <a href="" data-target="#editar" data-toggle="tab" class="nav-link">Editar</a>
                        </li>
                    <?php
                    }
                    ?>
                </ul>
                <div class="tab-content py-4">
                    <div class="tab-pane active" id="profile">
                        <h2 class="mb-3"><?= $model->nombre ?></h2>
                        <div class="row">
                            <div class="col-md-6">
                                <h4>Amigos: (<?= $model->getCountAmigos() ?>)</h4>
                                <h4>Registrado el:
                                    <?php
                                    $timestamp = strtotime($model->created_at);
                                    $fecha = getdate($timestamp);
                                    $fecha = date('d/m/Y', $timestamp);
                                    ?>
                                    <?= $fecha ?>
                                </h4>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="comentarios">
                        <!-- Pasar aqui los comentarios recibidos de otros usuarios -->
                        <?php
                        $emisor_id = Yii::$app->user->id;
                        $receptor_id = (int) Yii::$app->request->get('id');

                        ?>
                        <?=
                            $this->render('../comentarios-perfil/_crear', [
                                'model' => $modelPerfil,
                                'emisor_id' => $emisor_id,
                                'receptor_id' => $receptor_id,
                            ]);
                        ?>

                        <?php
                        foreach ($comentariosRecibidos as $comentario) {
                            $usuario = Usuarios::findOne($comentario->emisor_id);
                            dibujarComentario($usuario,$comentario);
                        }
                        ?>
                    </div>
                    <div class="tab-pane" id="editar">
                    </div>
                </div>
            </div>
            <div class="col-lg-4 order-1 text-center">
                <div class="row">
                    <div class="col">
                        <?=
                            Html::img(
                                $model->getImagen(),
                                ['alt' => 'Imagen de perfil', 'class' => 'img-fluid rounded']
                            )
                        ?>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col">
                        <?php
                        if ($model->id == Yii::$app->user->id) {
                        ?>
                            <?=
                                Html::a(
                                    'Modificar foto',
                                    ['usuarios/imagen', 'id' => $model->id],
                                    [
                                        'class' => 'btn btn-primary'
                                    ]
                                )
                            ?>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Fin codigo nuevo -->
<?php
function dibujarComentario($usuario, $comentario, $padre = false)
{
    if ($padre === true) {
?>
        <p>Respuesta al comentario anterior:</p>
        <div class="card mt-2 pb-4">
            <div>
                <div class="float-left mt-3 ml-3">
                    <?= Html::img($usuario->getImagen(), ['class' => 'rounded img-fluid']) ?>
                </div>
                <!-- botones -->
                <div class="float-right">
                    <div>
                        <!-- Opciones -->
                        <?php
                        $esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());

                        if (
                            $comentario->receptor_id == Yii::$app->user->id  ||
                            $comentario->emisor_id == Yii::$app->user->id || $esAdmin
                        ) {
                        ?>

                            <?=
                                Html::a(
                                    '',
                                    [
                                        'comentarios-perfil/responder',
                                        'receptor_id' => Yii::$app->user->id,
                                        'padre_id' => $comentario->id,
                                    ],
                                    [
                                        'class' => ' responder glyphicon glyphicon-share-alt text-decoration-none mt-2 mr-2',
                                        'data-toggle' => 'tooltip',
                                        'data-method' => 'POST',
                                        'title' => 'Responder',
                                    ],
                                )
                            ?>
                            <?php
                            if ($comentario->emisor_id === Yii::$app->user->id) {
                            ?>
                                <?=
                                    Html::a(
                                        '',
                                        [
                                            'comentarios-perfil/editar',
                                            'id' => $comentario->id,
                                        ],
                                        [
                                            'class' => 'glyphicon glyphicon-pencil mt-2 mr-2',
                                            'data-toggle' => 'tooltip',
                                            'data-method' => 'POST',
                                            'title' => 'Editar',
                                        ],
                                    )
                                ?>
                            <?php
                            }
                            ?>
                            <?=
                                Html::a(
                                    '',
                                    Url::to(['comentarios-perfil/delete', 'id' => $comentario->id]),
                                    [
                                        'class' => 'glyphicon glyphicon-trash mt-2 mr-2',
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
    } else {
    ?>
        <div class="card mt-2 pb-4">
            <div>
                <div class="float-left mt-3 ml-3">
                    <?= Html::img($usuario->getImagen(), ['class' => 'rounded img-fluid']) ?>
                </div>
                <!-- botones -->
                <div class="float-right">
                    <div>
                        <!-- Opciones -->
                        <?php
                        $esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());

                        if (
                            $comentario->receptor_id == Yii::$app->user->id  ||
                            $comentario->emisor_id == Yii::$app->user->id || $esAdmin
                        ) {
                        ?>

                            <?=
                                Html::a(
                                    '',
                                    [
                                        'comentarios-perfil/responder',
                                        'receptor_id' => Yii::$app->user->id,
                                        'padre_id' => $comentario->id,
                                    ],
                                    [
                                        'class' => ' responder glyphicon glyphicon-share-alt text-decoration-none mt-2 mr-2',
                                        'data-toggle' => 'tooltip',
                                        'data-method' => 'POST',
                                        'title' => 'Responder',
                                    ],
                                )
                            ?>
                            <?php
                            if ($comentario->emisor_id === Yii::$app->user->id) {
                            ?>
                                <?=
                                    Html::a(
                                        '',
                                        [
                                            'comentarios-perfil/editar',
                                            'id' => $comentario->id,
                                        ],
                                        [
                                            'class' => 'glyphicon glyphicon-pencil mt-2 mr-2',
                                            'data-toggle' => 'tooltip',
                                            'data-method' => 'POST',
                                            'title' => 'Editar',
                                        ],
                                    )
                                ?>
                            <?php
                            }
                            ?>
                            <?=
                                Html::a(
                                    '',
                                    Url::to(['comentarios-perfil/delete', 'id' => $comentario->id]),
                                    [
                                        'class' => 'glyphicon glyphicon-trash mt-2 mr-2',
                                        'data-toggle' => 'tooltip',
                                        'data-confirm' => '¿Está seguro de que desea eliminar este comentario?',
                                        'data-method' => 'POST',
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

<?php
}
?>