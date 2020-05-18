<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;

$sinFallos = <<<EOT
#login .container #login-row #login-column #login-box {
    margin-top: 120px;
    max-width: 600px;
    height: 330px;
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

$conFallos = <<<EOT
#login .container #login-row #login-column #login-box {
    margin-top: 120px;
    max-width: 600px;
    height: 375px;
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

if ($model->errors) {
    $this->registerCss($conFallos);
} else {
    $this->registerCss($sinFallos);
}
?>
<div id="login" class="site-login">
    <div class="container pt-5">
        <div id="login-row" class="row justify-content-center align-items-center">
            <div id="login-column" class="col-md-6">
                <div id="login-box" class="col-md-12">
                    <h3 class="text-center text-info">Login</h3>
                    <?php $form = ActiveForm::begin([
                        'id' => 'login-form',
                        // 'layout' => 'horizontal',
                        // 'fieldConfig' => [
                        //     'horizontalCssClasses' => ['wrapper' => 'col-sm-5'],
                        // ],
                    ]); ?>

                    <?= $form->field($model, 'login')->textInput([
                        'autofocus' => true,
                    ])->label('Usuario o e-mail') ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>


                    <?= Html::a(
                        '¿Ha olvidado su contraseña?',
                        Url::to(['/usuarios/resetear']),
                    ) ?>
                    <?= Html::a('Regístrate aquí', ['usuarios/registrar'], [
                        'class' => 'float-right',
                    ]) ?>
                    <div class="form-group">
                        <span>
                            <?= $form->field($model, 'rememberMe')->checkbox()->label('Recordarme') ?>
                        </span>
                    </div>

                    <?= Html::submitButton('Login', ['class' => 'form-control bg-primary text-white', 'name' => 'login-button']) ?>

                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>