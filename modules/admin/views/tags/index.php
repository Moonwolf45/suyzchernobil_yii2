<?php

use app\models\Tag;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Теги';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="tag-index">
    <p>
        <?= Html::a('Создать тег', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            'id',
            'title',
            'meta_keywords',
            'meta_description',
            [
                'attribute' => 'created_at',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->created_at, 'php:d.m.Y H:i:s');
                }
            ], [
                'attribute' => 'updated_at',
                'content' => function($data) {
                    return Yii::$app->formatter->asDate($data->updated_at, 'php:d.m.Y H:i:s');
                }
            ], [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Tag $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                 }
            ]
        ],
    ]); ?>
</div>
