<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use yii\helpers\Url;

$this->title = 'Login';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>

    <!-- <p>Rellene el siguiente formulario para hacer login:</p> -->

    <?php $form = ActiveForm::begin([
        'id' => 'login-form',
        'layout' => 'horizontal',
        'fieldConfig' => [
            'horizontalCssClasses' => ['wrapper' => 'col-sm-5'],
        ],
    ]); ?>

    <?= $form->field($model, 'login')->textInput(['autofocus' => true])->label('Usuario o e-mail') ?>

    <?= $form->field($model, 'password')->passwordInput() ?>

    <?= Html::a('¿Ha olvidado su contraseña?', 
    Url::to(['/usuarios/resetear']),[
        'class' => 'offset-sm-2', 
    ]) ?>

    <?= $form->field($model, 'rememberMe')->checkbox() ?>

    <div class="form-group">
        <div class="offset-sm-2">
            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>