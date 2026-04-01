<?php

/** @var app\models\NewsVideo $videoNews */

use yii\helpers\Html;
use yii\helpers\Url;

?>


<?php if (!empty($videoNews)): ?>
    <div class="container-fluid fh5co_video_news_bg pb-4">
        <div class="container animate-box" data-animate-effect="fadeIn">
            <div>
                <div class="fh5co_heading fh5co_heading_border_bottom pt-5 pb-2 mb-4 text-white">Видео новости</div>
            </div>
            <div>
                <div class="owl-carousel owl-theme" id="videoNews">
                    <?php foreach ($videoNews as $video): ?>
                        <div class="item px-2" itemscope itemprop="VideoObject" itemtype="https://schema.org/VideoObject">
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
                                        <span itemprop="name"><?= $video['title']; ?></span>
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
            <div class="row mx-0 animate-box" data-animate-effect="fadeInUp">
                <div class="col-12 text-center pb-4 pt-4 btn_mange_pagging">
                    <a href="<?= Url::to(['news/videos']); ?>">Больше видео-новостей</a>
                </div>
            </div>
        </div>
    </div>

<?php

$script = <<< JS
$(document).ready(function () {
    $('#videoNews').owlCarousel({
        loop: false,
        margin: 10,
        dots: false,
        nav: true,
        navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
        responsive: {
            0: {
                items: 1
            }, 600: {
                items: 2
            }, 1000: {
                items: 3
            }
        }
    });
    
    $('[data-fancybox]').fancybox({
        iframe : {
            preload : false
        }
    });
});
JS;

$this->registerJs($script); ?>

<?php endif; ?>