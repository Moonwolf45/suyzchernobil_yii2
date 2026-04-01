<?php

/** @var yii\web\View $this */
/** @var app\models\Subscribes $model */

$this->title = 'Update Subscribes: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Subscribes', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update'; ?>

<div class="subscribes-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
</div>
