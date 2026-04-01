<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\Documents $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Документы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="documents-view">
    <p>
        <?= Html::a('Обновить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить данный документ?',
                'method' => 'post',
            ],
        ]); ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'title',
            [
                'attribute' => 'image',
                'format' => ['html'],
                'value' => function($data) {
                    return Html::img('@web/' . $data->image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ],
            'file',
            [
                'attribute' => 'fasten',
                'value' => function($data) {
                    return $data->fasten === 1 ? 'Да' : 'Нет';
                }
            ], [
                'attribute' => 'isPdf',
                'value' => function($data) {
                    return $data->isPdf === 1 ? 'Да' : 'Нет';
                }
            ], [
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
        ],
    ]); ?>
</div>
