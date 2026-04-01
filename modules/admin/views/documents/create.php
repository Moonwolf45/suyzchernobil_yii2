<?php

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = 'Загрузить документ';
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="documents-create">
    <?= $this->render('_form', [
        'model' => $model
    ]); ?>
</div>
