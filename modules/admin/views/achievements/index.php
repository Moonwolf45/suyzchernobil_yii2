<?php

use app\models\Achievements;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\ActionColumn;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Достижение';
$this->params['breadcrumbs'][] = $this->title; ?>

<div class="achievements-index">
    <p>
        <?= Html::a('Добавить достижение', ['create'], ['class' => 'btn btn-success']); ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            'id',
            'title',
            [
                'attribute' => 'image',
                'content' => function($data) {
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
                'urlCreator' => function ($action, Achievements $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ]
        ]
    ]); ?>
</div>
