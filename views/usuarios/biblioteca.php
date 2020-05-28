<?php

use app\models\Juegos;
use yii\bootstrap4\Html;

if (!empty($juegos)) {
?>
    <div class="card-deck mt-4">
        <?php
        foreach ($juegos as $juego) {
            $modelJuego = Juegos::findOne($juego->juego_id);
        ?>
            <div class="card bg-dark">
                <div class="text-center mt-4">
                    <?= Html::img($modelJuego->getImagen()) ?>
                </div>
                <div class="card-body bg-white mt-4">
                    <h5 class="card-title">
                        <?= $modelJuego->titulo ?>
                    </h5>
                </div>
                <div class="card-footer bg-white">
                    <!-- <button>Descargar</button> -->
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
} else {
?>
    <div class="jumbotron jumbotron-fluid">
        <div class="container">
            <h1 class="display-4">Vaya, no has comprado ningún juego aún.</h1>
            <?=Html::a('Dirígete a la tienda para comprar juegos',['juegos/tienda'],[
                'class' => 'lead',
            ])?>
        </div>
    </div>
<?php
}
?>