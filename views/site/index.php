<?php

/* @var $this yii\web\View */

use yii\bootstrap4\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

$this->title = 'Coolgames';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Busque lo que quiera</h1>
        <p class="lead">Busque usuarios y juegos por título o género </p>

        <div>
            <?= Html::beginForm(['site/index'], 'get') ?>
            <div class="form-group">
                <?= Html::textInput('cadena', $cadena, ['class' => 'form-control']) ?>
            </div>
            <div class="form-group">
                <?= Html::submitButton('Buscar', ['class' => 'btn btn-primary']) ?>
            </div>
            <?= Html::endForm() ?>
        </div>
    </div>

    <div class="body-content">
        <?php if ($usuarios->totalCount > 0) : ?>
            <h3>Usuarios</h3>
            <div class="row">
                <?= GridView::widget([
                    'dataProvider' => $usuarios,
                    'columns' => [
                        'nombre',
                        [
                            'class' => ActionColumn::class,
                            'controller' => 'usuarios',
                            'template' => '{view}',
                        ],
                    ],
                ]) ?>
            </div>
        <?php endif ?>

        <?php if ($juegos->totalCount > 0) : ?>
            <h3>Juegos</h3>
            <div class="row">
                <?= GridView::widget([
                    'dataProvider' => $juegos,
                    'columns' => [
                        'titulo',
                        [
                            'class' => ActionColumn::class,
                            'controller' => 'juegos',
                            'template' => '{view}',
                        ],
                    ],
                ]) ?>
            </div>
        <?php endif ?>



    </div>
</div>