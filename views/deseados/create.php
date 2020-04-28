<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Deseados */

$this->title = 'Create Deseados';
$this->params['breadcrumbs'][] = ['label' => 'Deseados', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deseados-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
