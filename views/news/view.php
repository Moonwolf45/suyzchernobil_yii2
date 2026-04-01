<?php

/** @var app\models\News $news */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\helpers\Html;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = [
    'label' => $news['category']['title'],
    'url' => Url::to(['category/view', 'alias' => $news['category']['slug']])
];
$this->params['breadcrumbs'][] = ['label' => $news['title']];
?>

<div class="single" itemscope itemtype="https://schema.org/NewsArticle">
    <?php if(!empty($news['image'])): ?>
        <?php $image = explode('/', $news['image']); ?>

        <div style="display: none;" itemscope itemprop="image" itemtype="https://schema.org/ImageObject">
            <img itemprop="url contentUrl" src="<?= '/' . $image[0] . '/' . $image[1] . '/1920x1272_' . $image[2]; ?>">
        </div>

        <div id="fh5co-title-box" style="background-image: url(<?= '/' . $image[0] . '/' . $image[1]
            . '/1920x1272_' . $image[2]; ?>);" data-stellar-background-ratio="0.5">
    <?php else: ?>
        <div style="display: none;" itemscope itemprop="image" itemtype="https://schema.org/ImageObject">
            <img itemprop="url contentUrl" src="<?= '/images/placeHolder.png'; ?>">
        </div>

        <div id="fh5co-title-box" style="background-image: url(/images/placeHolder.png);" data-stellar-background-ratio="0.5">
    <?php endif; ?>
        <div class="overlay"></div>
        <div class="page-title">
            <span itemprop="datePublished" datetime="<?= date(DATE_W3C, $news['created_at']); ?>">
                <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?>
            </span>
            <span><i class="far fa-eye"></i> <?= $news['twisted_views']; ?></span>
            <h2 itemprop="headline"><?= $news['title']; ?></h2>
        </div>
    </div>

    <div id="fh5co-single-content" class="container-fluid pb-4 pt-4 paddding">
        <div class="container paddding">
            <div class="row mx-0">
                <?= BreadcrumbsSchemaWidget::widget([
                    'links' => $this->params['breadcrumbs'] ?? [],
                ]); ?>
            </div>
            <div class="row mx-0">
                <div class="col-md-8 animate-box" data-animate-effect="fadeInLeft">
                    <div class="news-text" itemprop="articleBody">
                        <?= $news['description']; ?>
                    </div>

                    <?php if (!empty($news['newsImages'])): ?>
                        <div class="news-gallery-block">
                            <?php foreach ($news['newsImages'] as $newsImage): ?>
                                <a class="news-gallery" data-fancybox="gallery" href="<?= '/' . $newsImage['image']; ?>">
                                    <?= Html::img('@web/' . $newsImage['image']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($news['tags'])): ?>
                        <div class="news-tags">
                            Теги:
                            <?php foreach ($news['tags'] as $key => $tag): ?>
                                <a href="<?= Url::to(['tags/view', 'alias' => $tag['slug']]); ?>">
                                    #<?= $tag['title']; ?><?= ($key + 1 < count($news['tags'])) ? ', ' : ''; ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?= $this->render('@app/views/_parts/right_bar'); ?>
            </div>

            <span style="display: none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                <a itemprop="url" href="<?= Url::base(true); ?>">
                    <span itemprop="name"><?= Yii::$app->params['title']; ?></span>
                </a>
            </span>
        </div>
    </div>

    <meta itemprop="dateModified" content="<?= date(DATE_W3C, $news['updated_at']); ?>" />
    <meta itemscope itemprop="mainEntityOfPage" itemType="https://schema.org/WebPage" itemid="<?= Url::current([], true); ?>" />
</div>

<?php

$script = <<< JS
    $(document).ready(function () {
        if (!navigator.userAgent.match(/Android|BlackBerry|iPhone|iPad|iPod|Opera Mini|IEMobile/i)) {
            $.stellar({
                horizontalScrolling: false
            });
        }
        
        $('[data-fancybox="gallery"]').fancybox();
    });
JS;

$this->registerJs($script); ?>