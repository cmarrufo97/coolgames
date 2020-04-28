<?php

use app\models\Deseados;
use app\models\Juegos;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->title = 'Tienda';
$this->params['breadcrumbs'][] = $this->title;

if ($deseados->totalCount > 0) {
?>
    <?= GridView::widget([
        'dataProvider' => $deseados,
        'columns' => [
            [
                'attribute' => 'imagen',
                'label' => 'Imagen',
                'value' => function ($model, $widget) {
                    $modelJuego = Juegos::findOne($model->juego_id);
                    return Html::img($modelJuego->getImagen());
                },
                'format' => 'html'
            ],
            [
                'attribute' => 'titulo',
                'label' => 'Título',
                'value' => function ($model, $widget) {
                    $tituloJuego = Juegos::find()->select('titulo')->where(['=', 'id', $model->juego_id])->scalar();

                    return Html::label($tituloJuego);
                },
                'format' => 'raw'
            ],
            [
                'attribute' => 'precio',
                'label' => 'Precio',
                'value' => function ($model, $widget) {
                    $precioJuego = Juegos::find()->select('precio')->where(['=', 'id', $model->juego_id])->scalar();

                    return Html::label($precioJuego);
                },
                'format' => 'raw'
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{eliminar}',
                'buttons' => [
                    'eliminar' => function ($url, $model) {
                        return Html::a(
                            'Eliminar',
                            Url::to([
                                'deseados/delete',
                                'id' => $model->id,
                            ]),
                            [
                                'class' => 'btn btn-sm btn-danger',
                                'data' => [
                                    'method' => 'POST',
                                    'confirm' => '¿Estás seguro de que desea eliminar este juego de su lista de deseados?'
                                ],
                            ]
                        );
                    },
                ],
            ],
        ],
    ]); ?>
<?php
}
