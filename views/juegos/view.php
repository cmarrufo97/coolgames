<?php

use app\models\Roles;
use app\models\Usuarios;
use yii\bootstrap4\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Juegos */

$this->title = $model->titulo;
$this->params['breadcrumbs'][] = ['label' => 'Juegos', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$esAdmin = (Roles::find()->select('id')->where(['=', 'rol', 'admin'])->scalar() === Usuarios::find()->select('rol_id')->where(['=', 'id', Yii::$app->user->id])->scalar());
?>
<div class="juegos-view">

    <!-- <h1><?= Html::encode($this->title) ?></h1> -->
    <?= Html::img($model->getImagen(), ['class' => '']) ?>
    <?php
    if ($esAdmin) {
    ?>
        <p>
            <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this item?',
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            // 'id',
            'titulo',
            // 'genero_id',
            [
                'attribute' => 'genero.denom',
                'label' => 'Género',
            ],
            'flanzamiento',
            'precio',
            'imagen',
            // 'created_at',
        ],
    ]) ?>

</div>