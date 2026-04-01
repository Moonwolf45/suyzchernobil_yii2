<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\NewsImage $model */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Доп. фотографии новостей', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-image-view">
    <p>
        <?= Html::a('Изменить', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную картинку?',
                'method' => 'post',
            ],
        ]); ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            [
                'attribute' => 'news_id',
                'value' => function($data) {
                    return $data->news->title;
                }
            ], [
                'attribute' => 'image',
                'format' => ['html'],
                'value' => function($data) {
                    return Html::img('@web/' . $data->image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ]
        ],
    ]) ?>

</div>
