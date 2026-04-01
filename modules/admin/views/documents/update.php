<?php

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = 'Обновить документ: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить'; ?>

<div class="documents-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
