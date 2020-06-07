<?php

use yii\bootstrap4\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Usuarios';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Usuarios', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            // 'id',
            'login',
            'nombre',
            'password',
            'email:email',
            //'auth_key',
            //'rol_id',
            //'estado_id',
            //'token',
            //'cod_verificacion',
            //'imagen:ntext',
            //'created_at',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}{perfil}',
                'buttons' => [
                    'perfil' => function ($url, $model, $key) {
                        return Html::a(
                            '<span class="glyphicon glyphicon-user ml-1"></span>',
                            ['usuarios/perfil', 'id' => $key],
                            [
                                'data-toggle' => 'tooltip',
                                'title' => 'Ver Perfil',
                            ],
                        );
                    },
                ],
            ],
        ],
    ]); ?>


</div>