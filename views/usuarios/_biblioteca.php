<?php
// Mostrar los juegos con cards.
use app\models\Generos;
use yii\bootstrap4\Html;
use yii\helpers\Url;

?>
<div class="text-center mt-4">
    <?= Html::img($juego->getImagen(), [
        'itemprop' => 'image',
    ]) ?>
</div>
<div class="card-body bg-white mt-4">
    <h5 class="card-title">
        <?= Html::a(
            $juego->titulo,
            Url::to(['juegos/view', 'id' => $juego->id]),
            ['itemprop' => 'name']
        )
        ?>
    </h5>
</div>

<div class="bg-white">
    <div class="text-center">
        <div>
            <h5>Género:
                <span>
                    <?= Generos::find()->select('denom')->where(['id' => $juego->genero_id])->scalar(); ?>
                </span>
            </h5>
        </div>
    </div>
</div>
<div class="card-footer bg-white">
    <?=
        $this->render('../juegos/_descargar', [
            'model' => $juego,
            'id' => $juego->id,
        ]);
    ?>
</div>