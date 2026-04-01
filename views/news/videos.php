<?php

/** @var app\models\NewsVideo $newsVideo */
/** @var yii\data\Pagination $pages */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\bootstrap5\LinkPager;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Видео-новости'];
?>

<div class="container-fluid pb-4 pt-4 paddding">
    <?php if (!empty($newsVideo)): ?>
        <div class="container paddding">
            <div class="row mx-0">
                <?= BreadcrumbsSchemaWidget::widget([
                    'links' => $this->params['breadcrumbs'] ?? [],
                ]); ?>
            </div>
            <div class="row mx-0">
                <div class="col-md-8 animate-box" data-animate-effect="fadeInLeft">
                    <div>
                        <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4">
                            Видео-новости
                        </div>
                    </div>

                    <div class="row pb-4">
                        <?php foreach ($newsVideo as $video): ?>
                            <div class="col-md-6" itemscope itemprop="VideoObject" itemtype="https://schema.org/VideoObject">
                                <div class="fh5co_hover_news_img">
                                    <div class="fh5co_hover_news_img_video_tag_position_relative">
                                        <div class="fh5co_news_img"></div>
                                        <div class="fh5co_hover_news_img_video_tag_position_absolute fh5co_hide_<?= $video['id']; ?>">
                                            <?= Html::img($video['preview_image']); ?>
                                        </div>
                                        <a class="fh5co_hover_news_img_video_tag_position_absolute_1" data-fancybox
                                           data-type="iframe" data-src="<?= $video['video']; ?>" href="javascript:;">
                                            <div class="fh5co_hover_news_img_video_tag_position_absolute_1_play_button_1">
                                                <div class="fh5co_hover_news_img_video_tag_position_absolute_1_play_button">
                                                    <span><i class="fa fa-play"></i></span>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="pt-2">
                                        <a data-fancybox data-type="iframe" data-src="<?= $video['video']; ?>"
                                           class="d-block fh5co_small_post_heading fh5co_small_post_heading_1" href="javascript:;">
                                            <span itemprop="name" style="color: #000;"><?= $video['title']; ?></span>
                                        </a>
                                        <div class="c_g">
                                            <i class="fa fa-clock-o"></i>
                                            <?= Yii::$app->formatter->asDate($video['created_at'], 'long'); ?><br>
                                        </div>
                                    </div>
                                </div>
                                <link itemprop="embedUrl" href="<?= $video['video']; ?>" />
                                <meta itemprop="contentUrl" content="<?= $video['video']; ?>" />
                                <meta itemprop="thumbnailUrl" content="<?= $video['preview_image']; ?>" />
                                <meta itemprop="uploadDate" content="<?= date(DATE_W3C, $video['created_at']); ?>" />
                                <meta itemprop="duration" content="<?= $video['duration']; ?>" />
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?= $this->render('@app/views/_parts/right_bar'); ?>
            </div>
            <div class="row mx-0">
                <?= LinkPager::widget([
                    'pagination' => $pages,
                    'options' => [
                        'tag' => false
                    ],
                    'listOptions' => [
                        'tag' => 'div',
                        'class' => 'col-12 text-center pb-4 pt-4'
                    ],
                    'linkContainerOptions' => [
                        'tag' => 'div',
                        'class' => ['btn_pagging']
                    ],
                    'linkOptions' => [
                        'class' => false
                    ],
                    'prevPageCssClass' => 'btn_mange_pagging',
                    'nextPageCssClass' => 'btn_mange_pagging',
                    'prevPageLabel' => '<i class="fa fa-long-arrow-left"></i> Назад',
                    'nextPageLabel' => 'Далее <i class="fa fa-long-arrow-right"></i>',
                ]); ?>
            </div>
        </div>
    <?php else: ?>
        <div class="container paddding">
            <div class="row mx-0">
                <div class="col-md-8 animate-box" data-animate-effect="fadeInLeft">
                    <h1>Видео-новостей не найдено.</h1>
                </div>
                <?= $this->render('@app/views/_parts/right_bar'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php $script = <<< JS
$(document).ready(function () {    
    $('[data-fancybox]').fancybox({
        iframe : {
            preload : false
        }
    });
});
JS;

$this->registerJs($script); ?>
