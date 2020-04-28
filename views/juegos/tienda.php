<?php

use yii\bootstrap4\Html;
use yii\helpers\Url;

$this->title = 'Tienda';
$this->params['breadcrumbs'][] = $this->title;



foreach ($juegos as $juego) {
?>
    <div class="card-deck mt-5">
        <div class="card bg-dark">
            <div class="text-center mt-4">
                <?= Html::img($juego->getImagen()) ?>
            </div>
            <div class="card-body bg-white mt-4">
                <h5 class="card-title"><?= Html::a(
                                            $juego->titulo,
                                            Url::to(['juegos/view', 'id' => $juego->id])
                                        ) ?></h5>
            </div>
            <div class="card-footer bg-white">
                <?= Html::a('Añadir a deseados', Url::to(['deseados/crear', 'id' => $juego->id]), [
                    'class' => 'btn btn-sm btn-primary'
                ]) ?>
                <button class="btn btn-sm btn-info">Añadir al carrito</button>
                <button class="btn btn-sm btn-success">Comprar</button>
            </div>
        </div>
    </div>
<?php
}
?>