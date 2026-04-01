<?php

namespace app\widgets;


use app\models\News;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\TagDependency;

class NewsPopularWidget extends Widget {

    public function run() {
        $dep = new TagDependency(['tags' => 'News']);

        $news = Yii::$app->cache->getOrSet(['news_widget'], function () {
            return News::find()->joinWith(['category'])->orderBy([News::tableName() . '.twisted_views' => SORT_DESC])
                ->limit(4)->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('newsPopularWidget', compact('news'));
    }
}