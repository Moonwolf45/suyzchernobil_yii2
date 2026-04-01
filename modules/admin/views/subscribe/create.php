<?php

/** @var yii\web\View $this */
/** @var app\models\Subscribes $model */

$this->title = 'Create Subscribes';
$this->params['breadcrumbs'][] = ['label' => 'Subscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="subscribes-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
