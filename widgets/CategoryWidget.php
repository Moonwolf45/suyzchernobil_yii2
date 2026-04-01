<?php

namespace app\widgets;

use app\models\Category;
use Yii;
use yii\bootstrap5\Widget;
use yii\caching\TagDependency;

class CategoryWidget extends Widget {

    public function run() {
        $dep = new TagDependency(['tags' => 'Category']);

        $categories = Yii::$app->cache->getOrSet(['categories_widget'], function () {
            return Category::find()->select(['title', 'slug'])->orderBy(['title' => SORT_ASC])->limit(8)
                ->asArray()->all();
        }, Yii::$app->params['cacheDuration'], $dep);

        return $this->render('categoryWidget', compact('categories'));
    }
}