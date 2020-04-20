<?php

use yii\bootstrap4\ActiveForm;

?>
<h1>Sube tu foto de perfil aquí:</h1>
<?php $form = ActiveForm::begin() ?>

<?= $form->field($model, 'imagen')
            ->fileInput(['class' => 'form-control'])
            ->label('Seleccione una imagen a continuación:') ?>

<button class="btn btn-primary">Modificar</button>

<?php ActiveForm::end() ?>