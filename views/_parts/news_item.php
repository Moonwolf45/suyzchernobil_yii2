<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\News $news */
/** @var int $position */

$newsUrlAbsolute = Url::to(['news/view', 'category_alias' => $news['category']['slug'], 'alias' => $news['slug']], true);
$newsUrl = Url::to(['news/view', 'category_alias' => $news['category']['slug'], 'alias' => $news['slug']]);
?>

<div class="row pb-4" itemprop="itemListElement" itemscope itemtype="https://schema.org">
    <meta itemprop="position" content="<?= $position; ?>" />
    <meta itemprop="url" content="<?= $newsUrlAbsolute; ?>" />

    <div itemscope itemtype="https://schema.org" style="display:none;">
        <meta itemprop="name" content="<?= Html::encode($news['title']); ?>" />
        <link itemprop="url" href="<?= $newsUrlAbsolute; ?>" />
        <?php if (!empty($news['image'])): ?>
            <?php $image = explode('/', $news['image']); ?>
            <meta itemprop="image" content="<?= Url::to('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2], true); ?>" />
        <?php else: ?>
            <meta itemprop="image" content="<?= Url::to('@web/images/placeHolder.png', true); ?>" />
        <?php endif; ?>
    </div>

    <div class="col-md-5">
        <div class="fh5co_hover_news_img">
            <div class="fh5co_news_img">
                <?php if (!empty($news['image'])): ?>
                    <?php $image = explode('/', $news['image']); ?>
                    <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2]); ?>
                <?php else: ?>
                    <?= Html::img('@web/images/placeHolder.png'); ?>
                <?php endif; ?>
            </div>
            <div></div>
        </div>
    </div>

    <div class="col-md-7 animate-box">
        <a href="<?= $newsUrl; ?>" class="fh5co_magna py-2">
            <?= Html::encode($news['title']); ?>
        </a>
        <div class="c_g">
            <a href="<?= $newsUrl; ?>" class="fh5co_mini_time py-3">
                <i class="fa fa-clock-o"></i>
                <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?><br>
                <i class="far fa-eye"></i> <?= $news['twisted_views']; ?>
            </a>
        </div>
        <div class="fh5co_consectetur">
            <?= Html::encode(mb_strimwidth(strip_tags($news['description']), 0, 120, '...')); ?>
        </div>
    </div>
</div>
