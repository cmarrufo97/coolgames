<?php

use app\models\Juegos;
use yii\bootstrap4\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$js = <<<EOT
    $(document).ready(function () {
        const formatter = new Intl.NumberFormat('es-ES', {
            style: 'currency',
            currency: 'EUR',
            minimumFractionDigits: 2
          });

        // let total = 0;
        // $('.table > tbody > tr').each(function (index, tr) {
        //     // console.log($(this).find('.precio').text());
        //     let valor = $(this).find('.precio').text();
        //     let precio = valor.replace('€','');

        //     total +=  parseInt(precio);
        // });

        let total = $('#precioTotal').val(); 

        $('.table tbody').append(`<tr><td><b>Total</b></td><td>\${formatter.format(total)}</td></tr>`);
    });
EOT;

$this->registerJs($js);


echo Html::hiddenInput('precioTotal', $precioTotal, [
    'id' => 'precioTotal',
]);

if ($lista->totalCount > 0) {
?>
    <?=
        GridView::widget([
            'dataProvider' => $lista,
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
                    'attribute' => 'precio:currency',
                    'label' => 'Precio',
                    'value' => function ($model, $widget) {
                        $precioJuego = Juegos::find()->select('precio')->where(['=', 'id', $model->juego_id])->scalar();

                        return Html::label(
                            Yii::$app->formatter->asCurrency($precioJuego),
                            null,
                            ['class' => 'precio']
                        );
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
                                    'carrito/delete',
                                    'id' => $model->id,
                                ]),
                                [
                                    'class' => 'btn btn-sm btn-danger',
                                    'data' => [
                                        'method' => 'POST',
                                        'confirm' => '¿Estás seguro de que desea eliminar este juego de su carrito?'
                                    ],
                                ]
                            );
                        },
                    ],
                ],
            ],
        ]);
    ?>
<?php

    echo Html::a('Continuar comprando', ['juegos/tienda'], [
        'class' => 'btn btn-sm btn-info text-white',
    ]);

    echo Html::a('Realizar Pago', null, [
        'class' => 'btn btn-sm btn-primary text-white ml-1',
    ]);
}
