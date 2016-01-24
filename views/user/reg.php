<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\Models\RegForm */
/* @var $form ActiveForm */
?>
<div class="user-reg">

    <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'login') ?>
        <?= $form->field($model, 'email') ?>
        <?= $form->field($model, 'pass')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Submit', ['class' => 'btn btn-primary']) ?>
        </div>
    <?php ActiveForm::end(); ?>

</div><!-- user-reg -->
