<?php

/** @var yii\web\View $this */
/** @var app\models\News $model */
/** @var app\models\Category $categories */
/** @var app\models\Tag $tags */

$this->title = 'Создание новости';
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-create">
    <?= $this->render('_form', ['model' => $model, 'categories' => $categories, 'tags' => $tags]); ?>
</div>
