<?php

/** @var app\models\News $news */

use yii\bootstrap5\Html;
use yii\helpers\Url;

?>

<div class="col-12 col-md-12 col-lg-4">
    <?php if (!empty($news)): ?>
        <div class="footer_main_title py-3">Последние измененные новости</div>
        <?php foreach ($news as $news_one): ?>
            <a href="<?= Url::to(['news/view', 'category_alias' => $news_one['category']['slug'],
                'alias' => $news_one['slug']]); ?>" class="footer_img_post_6">
                <?php if (!empty($news_one['image'])): ?>
                    <?php $image = explode('/', $news_one['image']); ?>
                    <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2], ['alt' => 'img']); ?>
                <?php else: ?>
                    <?= Html::img('@web/images/placeHolder.png', ['alt' => 'img']); ?>
                <?php endif; ?>
            </a>
        <?php endforeach; ?>
    <?php endif; ?>
</div>