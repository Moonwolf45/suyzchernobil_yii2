<?php

use yii\bootstrap5\LinkPager;
use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/** @var yii\data\ActiveDataProvider $dataProvider */

$this->title = 'Подписки';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subscribes-index">

    <h1><?= Html::encode($this->title); ?></h1>

<!--    <p>-->
<!--        --><?php //= Html::a('Create Subscribes', ['create'], ['class' => 'btn btn-success']) ?>
<!--    </p>-->

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'pager' => ['class' => LinkPager::class],
        'columns' => [
            'id',
            'email:email',
            'status',
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
//            ], [
//                'class' => ActionColumn::class,
//                'urlCreator' => function ($action, Subscribes $model, $key, $index, $column) {
//                    return Url::toRoute([$action, 'id' => $model->id]);
//                 }
            ]
        ]
    ]); ?>
</div>
