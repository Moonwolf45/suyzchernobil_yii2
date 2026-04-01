<?php

use yii\helpers\Html;
use yii\helpers\Url;

/** @var app\models\News $news */

?>

<div class="row pb-4" itemscope itemtype="https://schema.org/NewsArticle">
    <div class="col-md-5">
        <div class="fh5co_hover_news_img">
            <div class="fh5co_news_img">
                <?php if (!empty($news['image'])): ?>
                    <?php $image = explode('/', $news['image']); ?>
                    <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2], ['itemprop' => 'image']); ?>
                <?php else: ?>
                    <?= Html::img('@web/images/placeHolder.png', ['itemprop' => 'image']); ?>
                <?php endif; ?>
            </div>
            <div></div>
        </div>
    </div>
    <div class="col-md-7 animate-box">
        <a href="<?= Url::to(['news/view', 'category_alias' => $news['category']['slug'], 'alias' => $news['slug']]); ?>" class="fh5co_magna py-2" >
            <?= $news['title']; ?>
        </a>
        <span style="display: none;" itemprop="headline">
            <?= $news['title']; ?>
        </span>
        <div class="c_g">
            <a href="<?= Url::to(['news/view', 'category_alias' => $news['category']['slug'], 'alias' => $news['slug']]); ?>" class="fh5co_mini_time py-3">
                <i class="fa fa-clock-o"></i>
                <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?><br>
                <i class="far fa-eye"></i> <?= $news['twisted_views']; ?>
            </a>
        </div>
        <div class="fh5co_consectetur" itemprop="articleBody">
            <?= mb_strimwidth(strip_tags($news['description']), 0, 120, '...'); ?>
        </div>
        <div style="display: none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
            <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                <?= Html::img('@web/images/logo.png', ['itemprop' => 'url image']); ?>
            </div>
            <link itemprop="url" href="<?= Url::base(true); ?>">
            <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                <span itemprop="postalCode">640002</span>,
                <span itemprop="addressCountry">Россия</span>,
                <span itemprop="addressRegion">Курганская область</span>,
                <span itemprop="addressLocality">Курган</span>,
                <span itemprop="streetAddress">Максима Горького, 35</span>
            </div>
            <div>Телефон: <a itemprop="telephone" href="tel:+79125266546">+7 (912) 526-65-46</a></div>
            <meta itemprop="name" content="<?= Yii::$app->params['title']; ?>">
        </div>
        <span style="display: none;" itemprop="datePublished" content="<?= date(DATE_W3C, $news['created_at']); ?>">
            <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?>
        </span>
        <span style="display: none;" itemprop="dateModified" content="<?= date(DATE_W3C, $news['updated_at']); ?>">
            <?= Yii::$app->formatter->asDate($news['updated_at'], 'long'); ?>
        </span>
        <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage"
              itemid="<?= Url::to(['news/view', 'category_alias' => $news['category']['slug'], 'alias' => $news['slug']], true); ?>" />
    </div>
</div>
