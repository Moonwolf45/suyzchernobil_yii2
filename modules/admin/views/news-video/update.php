<?php

/** @var yii\web\View $this */
/** @var app\models\NewsVideo $model */

$this->title = 'Изменение видео-новости: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Видео-новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить'; ?>

<div class="news-video-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
