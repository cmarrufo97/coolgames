<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\RegistrarForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;

$this->title = 'Registrar usuario';
$this->params['breadcrumbs'][] = $this->title;

$css = <<<EOT
#login .container #login-row #login-column #login-box {
    margin-top: 120px;
    max-width: 600px;
    // height: 700px;
    border: 1px solid #9C9C9C;
    background-color: #EAEAEA;
  }
  #login .container #login-row #login-column #login-box #login-form {
    padding: 20px;
  }
  #login .container #login-row #login-column #login-box #login-form #register-link {
    margin-top: -85px;
  }
EOT;

$this->registerCss($css);
?>
<div id="login" class="site-login">
  <div class="container">
    <div id="login-row" class="row justify-content-center align-items-center">
      <div id="login-column" class="col-md-6">
        <div id="login-box" class="col-md-12">
          <h3 class="text-center text-info">Registrar usuario</h3>
          <p class="text-center">Introduzca los siguientes datos para registrarse:</p>
          <?php $form = ActiveForm::begin([
            'id' => 'login-form',
          ]); ?>

          <?= $form->field($model, 'login')->textInput(['autofocus' => true]) ?>
          <?= $form->field($model, 'nombre')->textInput() ?>
          <?= $form->field($model, 'password')->passwordInput() ?>
          <?= $form->field($model, 'password_repeat')->passwordInput() ?>
          <?= $form->field($model, 'email')->textInput() ?>

          <?= Html::submitButton('Registrarse', ['class' => 'form-control bg-primary text-white', 'name' => 'login-button']) ?>

          <?php ActiveForm::end(); ?>
        </div>
      </div>
    </div>
  </div>
</div>