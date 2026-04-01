<?php

/** @var yii\web\View $this */
/** @var app\models\NewsImage $model */
/** @var app\models\News $news */

$this->title = 'Создание доп. фотографии';
$this->params['breadcrumbs'][] = ['label' => 'Доп. фотографии новостей', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-image-create">
    <?= $this->render('_form', ['model' => $model, 'news' => $news]); ?>
</div>
