<?php

/** @var yii\web\View $this */
/** @var app\models\User $model */

$this->title = 'Create User';
$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="user-create">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
