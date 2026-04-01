<?php

use yii\helpers\Html;
use yii\bootstrap5\ActiveForm;

/** @var yii\web\View $this */
/** @var app\models\NewsVideo $model */
/** @var yii\bootstrap5\ActiveForm $form */
?>

<div class="news-video-form">
    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'title')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'meta_keywords')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'meta_description')->textInput(['maxlength' => true]); ?>

    <?= $form->field($model, 'video')->textInput(['maxlength' => true])
        ->hint('Пример: https://www.youtube.com/embed/4Y0MbLuRI8s'); ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
