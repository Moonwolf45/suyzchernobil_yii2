<?php

/** @var yii\web\View $this */
/** @var News $news */

use app\models\News;
use app\widgets\MainCategoryNewsWidget;
use app\widgets\VideoNewsWidget;
use yii\helpers\Html;
use yii\helpers\Url;

?>

<div class="container-fluid paddding">
    <div class="row mx-0">
        <?php $first_news = array_shift($news);
        $actual_news = array_slice($news, 0, 4);

        if (!empty($actual_news) && !empty($news)) {
            $news = array_slice($news, 5);
        }
        ?>

        <?php if(!empty($first_news)): ?>
            <div class="col-md-6 col-12 paddding animate-box" data-animate-effect="fadeIn" itemscope itemtype="https://schema.org/NewsArticle">
                <div class="fh5co_suceefh5co_height">
                    <?php if (!empty($first_news['image'])): ?>
                        <?php $image = explode('/', $first_news['image']); ?>
                        <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/960x640_' . $image[2], ['alt' => 'img',
                            'itemprop' => 'image']); ?>
                    <?php else: ?>
                        <?= Html::img('@web/images/placeHolder.png', ['alt' => 'img', 'itemprop' => 'image']); ?>
                    <?php endif; ?>
                    <div class="fh5co_suceefh5co_height_position_absolute"></div>
                    <div class="fh5co_suceefh5co_height_position_absolute_font">
                        <div>
                            <a href="<?= Url::to(['news/view', 'category_alias' => $first_news['category']['slug'],
                                'alias' => $first_news['slug']]); ?>" class="color_fff">
                                <i class="fa fa-clock-o"></i>
                                <?= Yii::$app->formatter->asDate($first_news['created_at'], 'long'); ?><br>
                                <i class="far fa-eye"></i> <?= $first_news['twisted_views']; ?>
                            </a>
                        </div>
                        <div>
                            <a href="<?= Url::to(['news/view', 'category_alias' => $first_news['category']['slug'],
                                'alias' => $first_news['slug']]); ?>" class="fh5co_good_font" itemprop="headline">
                                <?= $first_news['title']; ?>
                            </a>
                        </div>
                        <span style="display: none;" itemprop="datePublished" content="<?= date(DATE_W3C, $first_news['created_at']); ?>">
                            <?= Yii::$app->formatter->asDate($first_news['created_at'], 'long'); ?>
                        </span>
                        <span style="display: none;" itemprop="dateModified" content="<?= date(DATE_W3C, $first_news['updated_at']); ?>">
                            <?= Yii::$app->formatter->asDate($first_news['updated_at'], 'long'); ?>
                        </span>
                        <span style="display: none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                            <a itemprop="url" href="<?= Url::base(true); ?>">
                                <span itemprop="name"><?= Yii::$app->params['title']; ?></span>
                            </a>
                        </span>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="col-md-6">
            <div class="row">
                <?php if(!empty($actual_news)): ?>
                    <?php foreach ($actual_news as $actual_news_one): ?>
                        <div class="col-md-6 col-6 paddding animate-box" data-animate-effect="fadeIn" itemscope itemtype="https://schema.org/NewsArticle">
                            <div class="fh5co_suceefh5co_height_2">
                                <?php if (!empty($actual_news_one['image'])): ?>
                                    <?php $image = explode('/', $actual_news_one['image']); ?>
                                    <?= Html::img('@web/' . $image[0] . '/' . $image[1] . '/480x320_' . $image[2], [
                                        'alt' => 'img', 'itemprop' => 'image']); ?>
                                <?php else: ?>
                                    <?= Html::img('@web/images/placeHolder.png', ['alt' => 'img', 'itemprop' => 'image']); ?>
                                <?php endif; ?>
                                <div class="fh5co_suceefh5co_height_position_absolute"></div>
                                <div class="fh5co_suceefh5co_height_position_absolute_font_2">
                                    <div>
                                        <a href="<?= Url::to(['news/view', 'category_alias' => $actual_news_one['category']['slug'],
                                            'alias' => $actual_news_one['slug']]); ?>" class="color_fff">
                                            <i class="fa fa-clock-o"></i>
                                            <?= Yii::$app->formatter->asDate($actual_news_one['created_at'], 'long'); ?><br>
                                            <i class="far fa-eye"></i> <?= $actual_news_one['twisted_views']; ?>
                                        </a>
                                    </div>
                                    <div>
                                        <a href="<?= Url::to(['news/view', 'category_alias' => $actual_news_one['category']['slug'],
                                            'alias' => $actual_news_one['slug']]); ?>" class="fh5co_good_font_2" itemprop="headline">
                                            <?= $actual_news_one['title']; ?>
                                        </a>
                                    </div>
                                </div>
                                <span style="display: none;" itemprop="datePublished" content="<?= date(DATE_W3C, $actual_news_one['created_at']); ?>">
                                    <?= Yii::$app->formatter->asDate($actual_news_one['created_at'], 'long'); ?>
                                </span>
                                <span style="display: none;" itemprop="dateModified" content="<?= date(DATE_W3C, $actual_news_one['updated_at']); ?>">
                                    <?= Yii::$app->formatter->asDate($actual_news_one['updated_at'], 'long'); ?>
                                </span>
                                <span style="display: none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                                    <a itemprop="url" href="<?= Url::base(true); ?>">
                                        <span itemprop="name"><?= Yii::$app->params['title']; ?></span>
                                    </a>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= VideoNewsWidget::widget(); ?>
<?= MainCategoryNewsWidget::widget(); ?>

<div class="container-fluid pb-4 pt-4 paddding">
    <div class="container paddding">
        <div class="row mx-0">
            <div class="col-md-8 animate-box" data-animate-effect="fadeInLeft">
                <div>
                    <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4">Новости</div>
                </div>

                <?php if(!empty($news)): ?>
                    <?php foreach ($news as $news_one): ?>
                        <?= $this->render('@app/views/_parts/news_item', ['news' => $news_one]); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <?= $this->render('@app/views/_parts/right_bar'); ?>
        </div>
        <div class="row mx-0 animate-box" data-animate-effect="fadeInUp">
            <div class="col-12 text-center pb-4 pt-4 btn_mange_pagging">
                <a href="<?= Url::to(['news/archive']); ?>">Больше новостей</a>
            </div>
        </div>
    </div>
</div>