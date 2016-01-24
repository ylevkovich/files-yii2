<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\Models\LoginForm */
/* @var $form ActiveForm */
?>
<div class="user-login">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'login') ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-login -->
