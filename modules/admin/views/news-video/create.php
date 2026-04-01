<?php

/** @var yii\web\View $this */
/** @var app\models\NewsVideo $model */

$this->title = 'Создание видео-новости';
$this->params['breadcrumbs'][] = ['label' => 'Видео-новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-video-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
