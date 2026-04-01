<?php

namespace app\widgets;


use app\models\Category;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\TagDependency;

class MainCategoryNewsWidget extends Widget {

    public function run () {
        $dep = new TagDependency(['tags' => ['Category', 'News']]);

        $categoryNews = Yii::$app->cache->getOrSet(['mainCategoryNews_widget'], function () {
            return Category::find()->joinWith(['news' => function ($q) {
                $q->orderBy(['id' => SORT_DESC])->limit(8);
            }])->where(['main_status' => 1])->orderBy(['id' => SORT_DESC])->limit(8)->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('mainCategoryNewsWidget', compact('categoryNews'));
    }

}