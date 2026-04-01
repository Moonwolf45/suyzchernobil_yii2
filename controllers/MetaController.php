<?php

namespace app\controllers;


use Yii;
use yii\helpers\Url;
use yii\web\Controller;

class MetaController extends Controller {

    /**
     * Установка мета тегов
     *
     * @param null $title
     * @param null $keywords
     * @param null $description
     * @param null $image
     */
    protected function setMeta($title = null, $keywords = null, $description = null, $image = null, $type = 'website') {
        $this->view->title = $title;
        $this->view->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);
        $this->view->registerMetaTag(['name' => 'description', 'content' => $description]);

        $this->view->registerMetaTag(['property' => 'og:title', 'content' => $title]);
        $this->view->registerMetaTag(['property' => 'og:type', 'content' => $type]);

        $this->view->registerMetaTag(['property' => 'og:url', 'content' => Url::current([], true)]);
        $this->view->registerMetaTag(['property' => 'og:description ', 'content' => $description]);
        $this->view->registerMetaTag(['property' => 'og:locale', 'content' => str_replace('-', '_', Yii::$app->language)]);
        $this->view->registerMetaTag(['property' => 'og:url', 'content' => Url::base(true)]);

        $this->view->registerMetaTag(['itemprop' => 'name', 'content' => "$title"]);
        $this->view->registerMetaTag(['itemprop' => 'description', 'content' => "$description"]);

        if ($image !== null) {
            $this->view->registerMetaTag(['itemprop' => 'image', 'content' => Url::base(true) . "/" . $image]);
            $this->view->registerMetaTag(['property' => 'og:image', 'content' => Url::base(true) . "/" . $image]);
            $this->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => Url::base(true) . "/" . $image]);
            $this->view->registerMetaTag(['property' => 'og:image:type', 'content' => Url::base(true) . "/" . $image]);
            $this->view->registerMetaTag(['property' => 'og:image:width', 'content' => "900"]);
            $this->view->registerMetaTag(['property' => 'og:image:height', 'content' => "900"]);
        } else {
            $this->view->registerMetaTag(['itemprop' => 'image', 'content' => Url::base(true) . "/img/og_logo.png"]);
            $this->view->registerMetaTag(['property' => 'og:image', 'content' => Url::base(true) . "/img/og_logo.png"]);
            $this->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => Url::base(true) . "/img/og_logo.png"]);
            $this->view->registerMetaTag(['property' => 'og:image:type', 'content' => Url::base(true) . "/img/og_logo.png"]);
            $this->view->registerMetaTag(['property' => 'og:image:width', 'content' => "900"]);
            $this->view->registerMetaTag(['property' => 'og:image:height', 'content' => "900"]);
        }
    }
}