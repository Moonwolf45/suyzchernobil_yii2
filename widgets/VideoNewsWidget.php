<?php

namespace app\widgets;


use app\models\NewsVideo;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\TagDependency;

class VideoNewsWidget extends Widget {

    public function run () {
        $dep = new TagDependency(['tags' => 'NewsVideo']);

        $videoNews = Yii::$app->cache->getOrSet(['newsVideo_widget'], function () {
            return NewsVideo::find()->orderBy(['id' => SORT_DESC])->limit(8)
                ->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('videoNewsWidget', compact('videoNews'));
    }

}