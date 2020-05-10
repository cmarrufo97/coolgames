<?php

use app\services\Util;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$this->title = 'Mi Perfil';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="usuarios-perfil">
    <div class="container">
        <div class="row my-2">
            <div class="col-lg-8 order-lg-2">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a href="" data-target="#profile" data-toggle="tab" class="nav-link active">Mi Perfil</a>
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
            <div class="col-lg-4 order-lg-1 text-center">
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