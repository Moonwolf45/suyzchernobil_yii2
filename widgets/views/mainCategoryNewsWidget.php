<?php
/** @var yii\web\View $this */
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
                        <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4">
                            <?= Html::encode($category['title']); ?>
                        </div>
                    </div>
                    <div class="owl-carousel owl-theme js" id="slider<?= $category['id']; ?>">
                        <?php foreach ($category['news'] as $news): ?>
                            <?php
                                // Формируем абсолютную ссылку на новость для mainEntityOfPage
                                $newsUrlAbsolute = Url::to(['news/view', 'category_alias' => $category['slug'], 'alias' => $news['slug']], true);
                            $newsUrl = Url::to(['news/view', 'category_alias' => $category['slug'], 'alias' => $news['slug']]);
                            ?>

                            <div class="item px-2" itemscope itemtype="https://schema.org/NewsArticle">
                                <link itemprop="mainEntityOfPage" href="<?= $newsUrlAbsolute; ?>" />

                                <div class="fh5co_latest_trading_img_position_relative">
                                    <div class="fh5co_latest_trading_img">
                                        <?php if (!empty($news['image'])): ?>
                                            <?php
                                                $image = explode('/', $news['image']);
                                            $relativeImgPath = '@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2];
                                            ?>

                                            <?= Html::img($relativeImgPath, [
                                                    'class' => 'fh5co_img_special_relative',
                                                    'itemprop' => 'image'
                                            ]); ?>
                                        <?php else: ?>
                                            <?= Html::img('@web/images/placeHolder.png', [
                                                    'class' => 'fh5co_img_special_relative',
                                                    'itemprop' => 'image'
                                            ]); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="fh5co_latest_trading_img_position_absolute"></div>
                                    <div class="fh5co_latest_trading_img_position_absolute_1">
                                        <a href="<?= $newsUrl; ?>" class="text-white">
                                            <?= Html::encode($news['title']); ?>
                                        </a>

                                        <!-- Заголовок для микроразметки -->
                                        <a style="display:none;" itemprop="headline">
                                            <?= Html::encode($news['title']); ?>
                                        </a>

                                        <!-- Дата публикации -->
                                        <div class="fh5co_latest_trading_date_and_name_color" itemprop="datePublished" content="<?= date(DATE_W3C, $news['created_at']); ?>">
                                            <i class="fa fa-clock-o"></i>
                                            <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?>
                                        </div>

                                        <!-- Дата изменения -->
                                        <div style="display:none;" itemprop="dateModified" content="<?= date(DATE_W3C, $news['updated_at']); ?>">
                                            <?= Yii::$app->formatter->asDate($news['updated_at'], 'long'); ?>
                                        </div>

                                        <!-- Просмотры (не входят в Schema.org, просто вывод) -->
                                        <div class="fh5co_latest_trading_date_and_name_color">
                                            <i class="far fa-eye"></i>
                                            <?= $news['twisted_views']; ?>
                                        </div>

                                        <!-- Текст статьи / Краткое описание -->
                                        <div style="display:none;" itemprop="articleBody">
                                            <?= mb_strimwidth(strip_tags($news['description']), 0, 120, '...'); ?>
                                        </div>

                                        <!-- Автор (Организация) -->
                                        <div style="display: none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                                            <meta itemprop="name" content="<?= Html::encode(Yii::$app->params['title'] ?? 'Союз Чернобыль - Курган'); ?>">
                                            <link itemprop="url" href="<?= Url::base(true); ?>">

                                            <!-- Логотип организации -->
                                            <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                                                <img itemprop="url" src="<?= Url::to('@web/images/logo.png', true); ?>" alt="Logo">
                                            </div>

                                            <!-- Адрес организации -->
                                            <div itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                                <span itemprop="postalCode">640002</span>,
                                                <span itemprop="addressCountry">Россия</span>,
                                                <span itemprop="addressRegion">Курганская область</span>,
                                                <span itemprop="addressLocality">Курган</span>,
                                                <span itemprop="streetAddress">Максима Горького, 35</span>
                                            </div>
                                            <div>Телефон: <a itemprop="telephone" href="tel:+79125266546">+7 (912) 526-65-46</a></div>
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