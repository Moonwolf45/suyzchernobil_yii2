<?php

namespace app\widgets;

use app\models\News;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\DbDependency;

class LastModifiedNewsWidget extends Widget {

    public function run() {
        $dep = new DbDependency(['sql' => 'SELECT MAX(updated_at) FROM ' . News::tableName()]);

        $news = Yii::$app->cache->getOrSet(['last_modified_news_widget'], function () {
            return News::find()->joinWith(['category'])->orderBy([News::tableName() . '.updated_at' => SORT_DESC])
                ->limit(4)->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('lastModifiedNewsWidget', compact('news'));
    }

}