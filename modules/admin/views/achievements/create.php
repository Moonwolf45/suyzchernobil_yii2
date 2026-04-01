<?php

/** @var yii\web\View $this */
/** @var app\models\Achievements $model */

$this->title = 'Загрузить достижение';
$this->params['breadcrumbs'][] = ['label' => 'Достижение', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="achievements-create">
    <?= $this->render('_form', [
        'model' => $model
    ]); ?>
</div>
