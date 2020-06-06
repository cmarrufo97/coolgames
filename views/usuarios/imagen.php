<?php

use yii\bootstrap4\ActiveForm;
$this->title = 'Subir Imagen';
$this->params['breadcrumbs'][] = ['label' => 'Mi Perfil','url' => ['usuarios/perfil']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>Sube tu foto de perfil aquí:</h1>
<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>

<?= $form->field($model, 'imagen')
            ->fileInput(['class' => 'form-control'])
            ->label('Seleccione una imagen a continuación:') ?>

<button class="btn btn-primary">Modificar</button>

<?php ActiveForm::end() ?>