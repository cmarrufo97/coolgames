<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Peticiones */

$this->title = 'Create Peticiones';
$this->params['breadcrumbs'][] = ['label' => 'Peticiones', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="peticiones-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
