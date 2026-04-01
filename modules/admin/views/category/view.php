<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Category $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Категории', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="category-view">
    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную категорию?',
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
                'attribute' => 'main_status',
                'value' => function($model) {
                    return $model->main_status === 1 ? 'Да' : 'Нет';
                }
            ], [
                'attribute' => 'created_at',
                'value' => function($model) {
                    return Yii::$app->formatter->asDate($model->created_at, 'php:d.m.Y H:i:s');
                }
            ], [
                'attribute' => 'updated_at',
                'value' => function($model) {
                    return Yii::$app->formatter->asDate($model->updated_at, 'php:d.m.Y H:i:s');
                }
            ]
        ],
    ]); ?>
</div>
