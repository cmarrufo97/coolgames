<?php

use app\models\ComentariosPerfil;
use app\models\Roles;
use app\models\Usuarios;
use app\services\Comentario;
use app\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Mi Perfil';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-perfil">
    <div class="container">
        <div class="row my-2">
            <div class="col-lg-8 order-2 mt-2">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="" data-target="#comentarios" data-toggle="tab" class="nav-link active">Comentarios</a>
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
                    <div class="tab-pane active" id="comentarios">
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
                        <?php procesarComentarios($comentariosRecibidos); ?>
                    </div>
                    <div class="tab-pane" id="editar">
                        <?=
                            $this->render('_modificar', [
                                'model' => $model,
                            ])
                        ?>
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
                            <?=
                                Html::a(
                                    'Eliminar',
                                    ['usuarios/eliminar-foto'],
                                    [
                                        'class' => 'btn btn-danger text-white',
                                        'data' => [
                                            'method' => 'POST',
                                            'params' => ['id' => $model->id],
                                        ],
                                    ]
                                )
                            ?>
                        <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="mt-4">
                    <table class="table table-sm">
                        <caption>
                            <p class="lead datos-perfil">Datos de perfil:</p>
                        </caption>
                        <tbody>
                            <tr>
                                <td class="border-0"><strong class="text-right">Nombre:</strong></td>
                                <td class="border-0"><?= $model->nombre ?></td>
                            </tr>
                            <tr>
                                <td class="border-0"><strong class="text-right">Amigos:</strong></td>
                                <td class="border-0"><?= $model->getCountAmigos() ?></td>
                            </tr>
                            <tr>
                                <td class="border-0"><strong class="text-right">Registrado el:</strong></td>
                                <td class="border-0"><?= Yii::$app->formatter->asDate($model->created_at) ?></td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
function dibujarComentario($comentario, $nivel = 0)
{
    $modelComentario = ComentariosPerfil::findOne($comentario->id);
    $usuario = Usuarios::findOne($modelComentario->emisor_id);

    $hijos = $comentario->getChildren();

?>
    <div class="card mt-2 pb-4 pr-2 ml-<?= $nivel ?>">
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

                    echo Html::a(
                        '',
                        [
                            'comentarios-perfil/responder',
                        ],
                        [
                            'class' => ' responder glyphicon glyphicon-share-alt text-decoration-none mt-2 mr-2',
                            'data' => [
                                'method' => 'POST',
                                'toggle' => 'tooltip',
                                'params' => [
                                    'receptor_id' => $modelComentario->receptor_id,
                                    'padre_id' => $modelComentario->id,
                                ],
                            ],
                            'title' => 'Responder',
                        ],
                    );

                    if (
                        $modelComentario->receptor_id == Yii::$app->user->id  ||
                        $modelComentario->emisor_id == Yii::$app->user->id || $esAdmin
                    ) {
                    ?>
                        <?php
                        if ($modelComentario->emisor_id === Yii::$app->user->id) {
                        ?>
                            <?=
                                Html::a(
                                    '',
                                    [
                                        'comentarios-perfil/editar',
                                    ],
                                    [
                                        'class' => 'glyphicon glyphicon-pencil mt-2 mr-2',
                                        'data' => [
                                            'method' => 'POST',
                                            'toggle' => 'tooltip',
                                            'params' => [
                                                'id' => $modelComentario->id,
                                            ],
                                        ],
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
                                Url::to(['comentarios-perfil/delete']),
                                [
                                    'class' => 'glyphicon glyphicon-trash mt-2 mr-2',
                                    'data' => [
                                        'method' => 'POST',
                                        'confirm' => '¿Está seguro de que desea eliminar este comentario?',
                                        'toggle' => 'tooltip',
                                        'params' => [
                                            'id' => $modelComentario->id,
                                        ],
                                    ],
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
                <p class="text-muted time ml-3">
                    <?=
                        Yii::$app->formatter->asDatetime($modelComentario->created_at);
                    ?>
                </p>
                <p class="text-muted time ml-3 mt-n3">
                    <?=
                        $modelComentario->edited_at != null ?
                            'Editado el: ' . Yii::$app->formatter->asDatetime($modelComentario->edited_at) : '';
                    ?>
                </p>
                <div class="mt-4 ml-3">
                    <p><?= $modelComentario->comentario ?></p>
                </div>
            </div>
        </div>
        <?php
        foreach ($hijos as $hijo) {
            dibujarComentario($hijo, $nivel + 1);
        }
        ?>
    </div>
<?php
}
function procesarComentarios($comentariosRecibidos)
{
    $workingMemory = [];
    $unprocessedRows = [];

    $unprocessedRows = $comentariosRecibidos;

    do {
        $row = $unprocessedRows;
        $unprocessedRows = [];
        foreach ($row as $record) {
            $id = $record->id;
            $content = $record->comentario;
            $parentId = $record->padre_id;

            $comentario = new Comentario($id, $content);

            if ($parentId === null) {
                $workingMemory[$id] = $comentario;
            } else if (isset($workingMemory[$parentId])) {
                $parentComment = $workingMemory[$parentId];
                $parentComment->addChild($comentario);
                $workingMemory[$id] = $comentario;
            } else {
                $unprocessedRows[] = $record;
            }
        }
    } while (count($unprocessedRows) > 0);
    foreach ($workingMemory as $aComment) {
        if ($aComment->isRoot === true) {
            dibujarComentario($aComment);
        }
    }
}
?>