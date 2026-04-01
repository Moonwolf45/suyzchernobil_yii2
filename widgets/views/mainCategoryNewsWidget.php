<?php

/** @var app\models\Category $categoryNews */

use yii\helpers\Html;
use yii\helpers\Url;

?>

<?php if (!empty($categoryNews)): ?>
    <?php foreach ($categoryNews as $category): ?>
        <?php if (!empty($category['news'])): ?>
            <div class="container-fluid pt-3 pb-4">
                <div class="container animate-box" data-animate-effect="fadeIn">
                    <div>
                        <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4"><?= $category['title']; ?></div>
                    </div>
                    <div class="owl-carousel owl-theme js" id="slider<?= $category['id']; ?>">
                        <?php foreach ($category['news'] as $news): ?>
                            <div class="item px-2" itemscope itemtype="https://schema.org/NewsArticle">
                                <div class="fh5co_latest_trading_img_position_relative">
                                    <div class="fh5co_latest_trading_img">
                                        <?php if (!empty($news['image'])): ?>
                                            <?php $image = explode('/', $news['image']); ?>
                                            <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2],
                                                ['class' => 'fh5co_img_special_relative', 'itemprop' => 'image']); ?>
                                        <?php else: ?>
                                            <?= Html::img('@web/images/placeHolder.png', ['class' => 'fh5co_img_special_relative',
                                                'itemprop' => 'image']); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fh5co_latest_trading_img_position_absolute"></div>
                                    <div class="fh5co_latest_trading_img_position_absolute_1">
                                        <a href="<?= Url::to(['news/view', 'category_alias' => $category['slug'],
                                            'alias' => $news['slug']]); ?>" class="text-white">
                                            <?= $news['title']; ?>
                                        </a>
                                        <span style="display: none;" itemprop="headline">
                                            <?= $news['title']; ?>
                                        </span>
                                        <div class="fh5co_latest_trading_date_and_name_color" itemprop="datePublished" content="<?= date(DATE_W3C, $news['created_at']); ?>">
                                            <i class="fa fa-clock-o"></i>
                                            <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?>
                                        </div>
                                        <span style="display: none;" itemprop="dateModified" content="<?= date(DATE_W3C, $news['updated_at']); ?>">
                                            <?= Yii::$app->formatter->asDate($news['updated_at'], 'long'); ?>
                                        </span>
                                        <div class="fh5co_latest_trading_date_and_name_color">
                                            <i class="far fa-eye"></i>
                                            <?= $news['twisted_views']; ?>
                                        </div>
                                        <div style="display: none;" itemprop="articleBody">
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
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <?php $this->registerJs("
                    $(document).ready(function () {
                        $('#slider" . $category['id'] . "').owlCarousel({
                            loop: false,
                            margin: 10,
                            dots: false,
                            nav: true,
                            navText: [\"<i class='fa fa-angle-left'></i>\", \"<i class='fa fa-angle-right'></i>\"],
                            responsive: {
                                0: {
                                    items: 1
                                }, 600: {
                                    items: 3
                                }, 1000: {
                                    items: 4
                                }
                            }
                        });
                    });
                "); ?>
            </div>
        <?php endif; ?>
    <?php endforeach; ?>
<?php endif; ?>