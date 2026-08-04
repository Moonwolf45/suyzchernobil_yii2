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
    <link itemprop="mainEntityOfPage" href="<?= Url::current([], true); ?>" />

    <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization" style="display:none;">
        <meta itemprop="name" content="<?= Html::encode(Yii::$app->params['title']); ?>" />
        <link itemprop="url" href="<?= Url::base(true); ?>" />
    </div>

    <?php if(!empty($news['image'])): ?>
        <?php $image = explode('/', $news['image']); ?>
        <?php $imageUrl = '/' . $image[0] . '/' . $image[1] . '/1920x1272_' . $image[2]; ?>
    <?php else: ?>
        <?php $imageUrl = '/images/placeHolder.png'; ?>
    <?php endif; ?>

    <div style="display: none;" itemscope itemtype="https://schema.org/ImageObject" itemprop="image">
        <link itemprop="url" href="<?= Url::base(true) . $imageUrl; ?>">
        <link itemprop="contentUrl" href="<?= Url::base(true) . $imageUrl; ?>">
    </div>

    <div id="fh5co-title-box" style="background-image: url(<?= $imageUrl; ?>);" data-stellar-background-ratio="0.5">
        <div class="overlay"></div>
        <div class="page-title">
            <time itemprop="datePublished" datetime="<?= date(DATE_W3C, $news['created_at']); ?>">
                <?= Yii::$app->formatter->asDate($news['created_at'], 'long'); ?>
            </time>
            <span><i class="far fa-eye"></i> <?= $news['twisted_views']; ?></span>
            <h2 itemprop="headline"><?= Html::encode($news['title']); ?></h2>
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

            <div style="display:none;" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                <meta itemprop="name" content="<?= Html::encode(Yii::$app->params['title']); ?>" />
                <link itemprop="url" href="<?= Url::base(true); ?>" />
            </div>
        </div>
    </div>

    <meta itemprop="dateModified" content="<?= date(DATE_W3C, $news['updated_at']); ?>" />
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