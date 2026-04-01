<?php

/** @var yii\web\View $this */
/** @var app\models\Tag $model */

$this->title = 'Изменение тега: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Изменить'; ?>

<div class="tag-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]); ?>
</div>
