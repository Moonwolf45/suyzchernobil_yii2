<?php

use app\models\NewsImage;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Дополнительные фотографии новостей';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-image-index">
    <p>
        <?= Html::a('Создать доп. фотографию', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            'id',
            [
                'attribute' => 'news_id',
                'content' => function($data) {
                    return $data->news->title;
                }
            ], [
                'attribute' => 'image',
                'content' => function($data) {
                    return Html::img('@web/' . $data->image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ], [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, NewsImage $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>
</div>
