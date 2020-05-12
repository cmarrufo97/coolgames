<?php

use yii\bootstrap4\Html;

/* @var $this yii\web\View */
/* @var $model app\models\ComentariosPerfil */

$this->title = 'Create Comentarios Perfil';
$this->params['breadcrumbs'][] = ['label' => 'Comentarios Perfils', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="comentarios-perfil-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
