<?php

/** @var yii\web\View $this */
/** @var app\models\NewsImage $model */
/** @var app\models\News $news */

$this->title = 'Обновление доп. фотографии новостей: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Доп. фотографии новостей', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Обновление'; ?>

<div class="news-image-update">
    <?= $this->render('_form', ['model' => $model, 'news' => $news]); ?>
</div>
