<?php

namespace app\widgets;


use app\models\Tag;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\TagDependency;

class TagsWidget extends Widget {

    public function run() {
        $dep = new TagDependency(['tags' => 'Tag']);

        $tags = Yii::$app->cache->getOrSet(['tags_widget'], function () {
            return Tag::find()->select(['title', 'slug'])->orderBy(['title' => SORT_ASC])->limit(15)
                ->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('tagsWidget', compact('tags'));
    }
}