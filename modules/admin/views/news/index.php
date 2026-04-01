<?php

use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Новости';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-index">
    <p>
        <?= Html::a('Создать новость', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            'id',
            'title',
            [
                'attribute' => 'category_id',
                'content' => function($data) {
                    return $data->category->title;
                }
            ], [
                'attribute' => 'tags',
                'format' => ['html'],
                'content' => function($data) {
                    $tags = '';
                    if (!empty($data->tags)) {
                        foreach ($data->tags as $tag) {
                            $tags .= $tag->title . '<br>';
                        }
                    }

                    return $tags;
                }
            ], [
                'attribute' => 'image',
                'content' => function($data) {
                    return Html::img('@web/' . $data->image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ],
            'views',
            'twisted_views',
            [
                'attribute' => 'published_at_vk',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->published_at_vk, 'php:d.m.Y H:i:s');
                }
            ], [
                'attribute' => 'published_at_ok',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->published_at_ok, 'php:d.m.Y H:i:s');
                }
            ], [
                'class' => ActionColumn::class,
                'template' => '{publish} {view} {update} {delete}',
                'buttons' => [
                    'publish' => function ($url, $model) {
                        return Html::a('<i class="fa fa-upload"></i>', $url, [
                            'title' => 'Публиковать'
                        ]);
                    },
                ]
            ]
        ]
    ]); ?>
</div>
