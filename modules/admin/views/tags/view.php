<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Tag $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Tags', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="tag-view">
    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данный тег?',
                'method' => 'post',
            ],
        ]); ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            'slug',
            'meta_keywords',
            'meta_description',
            [
                'attribute' => 'created_at',
                'value' => function($data) {
                    return Yii::$app->formatter->asDate($data->created_at, 'php:d.m.Y H:i:s');
                }
            ], [
                'attribute' => 'updated_at',
                'value' => function($data) {
                    return Yii::$app->formatter->asDate($data->updated_at, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]); ?>
</div>
