<?php

/** @var app\models\News $news */

use yii\bootstrap5\Html;
use yii\helpers\Url;

?>

<?php if (!empty($news)): ?>
    <div>
        <div class="fh5co_heading fh5co_heading_border_bottom pt-3 py-2 mb-4">Самые популярные</div>
    </div>
    <?php foreach ($news as $news_one): ?>
        <div itemscope itemtype="https://schema.org/NewsArticle">
            <a href="<?= Url::to(['news/view', 'category_alias' => $news_one['category']['slug'], 'alias' => $news_one['slug']]); ?>"
               class="row pb-3">
                <div class="col-5 align-self-center text-center">
                    <?php if (!empty($news_one['image'])): ?>
                        <?php $image = explode('/', $news_one['image']); ?>
                        <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2], ['alt' => 'img',
                            'class' => 'fh5co_most_trading', 'itemprop' => 'image']); ?>
                    <?php else: ?>
                        <?= Html::img('@web/images/placeHolder.png', ['alt' => 'img', 'class' => 'fh5co_most_trading',
                            'itemprop' => 'image']); ?>
                    <?php endif; ?>
                </div>
                <div class="col-7 paddding">
                    <div class="most_fh5co_treding_font" itemprop="headline">
                        <?= $news_one['title']; ?>
                    </div>
                    <div class="most_fh5co_treding_font_123" itemprop="datePublished" content="<?= date(DATE_W3C, $news_one['created_at']); ?>">
                        <?= Yii::$app->formatter->asDate($news_one['created_at'], 'long'); ?>
                    </div>
                    <span style="display: none;" itemprop="dateModified" content="<?= date(DATE_W3C, $news_one['updated_at']); ?>">
                        <?= Yii::$app->formatter->asDate($news_one['updated_at'], 'long'); ?>
                    </span>
                    <div class="most_fh5co_treding_font_123">
                        <i class="far fa-eye"></i> <?= $news_one['twisted_views']; ?>
                    </div>
                </div>
            </a>
            <div style="display: none;" itemprop="articleBody">
                <?= mb_strimwidth(strip_tags($news_one['description']), 0, 120, '...'); ?>
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
        </div>
    <?php endforeach; ?>
<?php endif; ?>