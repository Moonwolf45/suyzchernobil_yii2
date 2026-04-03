<?php

use app\models\NewsImage;
use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var app\models\News $model */
/** @var app\models\NewsImage $gallery */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => 'Новости', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="news-view">
    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']); ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить данную новость?',
                'method' => 'post',
            ],
        ]); ?>

        <?= Html::a('<i class="fa fa-upload"></i> <i class="fa fa-vk"></i>', ['retry-vk-publish', 'id' => $model->id], [
                'title' => 'Публиковать в Vk',
                'class' => 'btn btn-primary'
        ]); ?>

        <?= Html::a('<i class="fa fa-upload"></i> <i class="fa fa-odnoklassniki"></i>', ['retry-ok-publish', 'id' => $model->id], [
                'title' => 'Публиковать в OK',
                'class' => 'btn btn-primary'
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
                'attribute' => 'category_id',
                'value' => function($data) {
                    return $data->category->title;
                }
            ], [
                'attribute' => 'tags',
                'format' => ['html'],
                'value' => function($data) {
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
                'format' => ['html'],
                'value' => function($data) {
                    return Html::img('@web/' . $data->image, [
                        'style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;'
                    ]);
                }
            ],
            'description:html',
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
            ], [
                'attribute' => 'published_at_vk',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->published_at_vk, 'php:d.m.Y H:i:s');
                }
            ], [
                'attribute' => 'published_at_ok',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->published_at_ok, 'php:d.m.Y H:i:s');
                }
            ]
        ]
    ]); ?>

    <?= GridView::widget([
        'dataProvider' => $gallery,
        'columns' => [
            'id',
            [
                'attribute' => 'news_id',
                'content' => function() use ($model) {
                    return $model->title;
                }
            ], [
                'attribute' => 'image',
                'content' => function($data) {
                    return Html::img('@web/' . $data->image, ['style' => 'max-width: 400px; max-height: 400px; width: auto; height: auto;']);
                }
            ], [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, NewsImage $ni_model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $ni_model->id]);
                }
            ],
        ],
    ]); ?>
</div>
