<?php

/** @var yii\web\View $this */
/** @var app\models\Category $model */

$this->title = 'Изменение категории: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить'; ?>

<div class="category-update">
    <?= $this->render('_form', [
        'model' => $model
    ]); ?>
</div>
