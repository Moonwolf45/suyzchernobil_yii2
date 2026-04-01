<?php

namespace app\models\traits;

use Yii;
use yii\helpers\Url;

trait MetaTrait {

    /**
     * Установка мета тегов
     *
     * @param $controller
     * @param string|null $title
     * @param string|null $keywords
     * @param string|null $description
     * @param string|null $image
     * @param string $type
     */
    protected function setMeta($controller, string $title = null, string $keywords = null, string $description = null,
                               string $image = null, string $type = 'website'): void {
        if ($title === null) {
            $title = Yii::$app->params['title'];
        }
        if ($keywords === null) {
            $keywords = Yii::$app->params['keywords'];
        }
        if ($description === null) {
            $description = Yii::$app->params['description'];
        }
        $controller->view->title = $title;
        $controller->view->registerMetaTag(['name' => 'keywords', 'content' => $keywords]);
        $controller->view->registerMetaTag(['name' => 'description', 'content' => $description]);

        $controller->view->registerMetaTag(['itemprop' => 'description', 'content' => "$description"]);

        $controller->view->registerMetaTag(['property' => 'og:site_name', 'content' => Yii::$app->params['title']]);
        $controller->view->registerMetaTag(['property' => 'og:title', 'content' => $title]);
        $controller->view->registerMetaTag(['property' => 'og:type', 'content' => $type]);

        $controller->view->registerMetaTag(['property' => 'og:url', 'content' => Url::current([], true)]);
        $controller->view->registerMetaTag(['property' => 'og:description ', 'content' => $description]);
        $controller->view->registerMetaTag(['property' => 'og:locale', 'content' => str_replace('-', '_', Yii::$app->language)]);

        if ($image !== null && $image !== '') {
            $imageAttr = getimagesize($image);
            $imageType = image_type_to_mime_type(exif_imagetype($image));

            $controller->view->registerMetaTag(['itemprop' => 'image', 'content' => Url::base(true) . '/' . $image]);
            $controller->view->registerMetaTag(['property' => 'og:image', 'content' => Url::base(true) . '/' . $image]);
            $controller->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => Url::base(true) . '/' . $image]);
            $controller->view->registerMetaTag(['property' => 'og:image:type', 'content' => $imageType]);
            $controller->view->registerMetaTag(['property' => 'og:image:width', 'content' => $imageAttr[0]]);
            $controller->view->registerMetaTag(['property' => 'og:image:height', 'content' => $imageAttr[1]]);
        } else {
            $defaultImage = Url::base(true) . '/images/logo.png';
            $imageType = image_type_to_mime_type(exif_imagetype($defaultImage));

            $controller->view->registerMetaTag(['itemprop' => 'image', 'content' => $defaultImage]);
            $controller->view->registerMetaTag(['property' => 'og:image', 'content' => $defaultImage]);
            $controller->view->registerMetaTag(['property' => 'og:image:secure_url', 'content' => $defaultImage]);
            $controller->view->registerMetaTag(['property' => 'og:image:type', 'content' => $imageType]);
            $controller->view->registerMetaTag(['property' => 'og:image:width', 'content' => '250']);
            $controller->view->registerMetaTag(['property' => 'og:image:height', 'content' => '250']);
        }
    }
}