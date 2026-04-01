<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\NewsVideo $model */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Видео-новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-video-view">
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить данную новость?',
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
            'video',
            [
                'attribute' => 'preview_image',
                'content' => function($data) {
                    return Html::img('@web/' . $data->preview_image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ],
            'duration',
            'views',
            'twisted_views',
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
        ],
    ]); ?>
</div>
