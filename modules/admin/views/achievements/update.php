<?php

/** @var yii\web\View $this */
/** @var app\models\Achievements $model */

$this->title = 'Обновить достижение: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Достижение', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновить'; ?>

<div class="achievements-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
