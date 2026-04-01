<?php

/** @var yii\web\View $this */
/** @var app\models\News $model */
/** @var app\models\Category $categories */
/** @var app\models\Tag $tags */
/** @var app\models\NewsImage $gallery */

$this->title = 'Изменение новости: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить'; ?>

<div class="news-update">
    <?= $this->render('_form', ['model' => $model, 'categories' => $categories, 'tags' => $tags,
        'gallery' => $gallery]); ?>
</div>
