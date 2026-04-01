<?php

/** @var app\models\Documents $documents */
/** @var yii\data\Pagination $pages */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Наши достижения'];
?>

<div class="container-fluid pb-4 pt-4 paddding">
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
                        Наши достижения
                    </div>
                </div>

                <?php if (!empty($ourAchievements)): ?>
                    <div class="news-gallery-block">
                        <?php foreach ($ourAchievements as $achievement): ?>
                            <?php $isPdf = $achievement['isPdf']; ?>
                            <a class="news-gallery" <?= $isPdf ?: 'data-fancybox="gallery"'; ?> href="<?= '/' . $achievement['file']; ?>"
                                data-caption="<?= $achievement['title']; ?>">

                                <?php if ($achievement['fasten']): ?>
                                    <span class="image-fasten">Закреплён</span>
                                <?php endif; ?>

                                <?php if ($isPdf): ?>
                                    <?php if ($achievement['image'] !== null): ?>
                                        <?= Html::img('@web/' . $achievement['image'], ['alt' => $achievement['title']]); ?>
                                    <?php else: ?>
                                        <?= Html::img('@web/images/pdf.png', ['alt' => $achievement['title']]); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= Html::img('@web/' . $achievement['file'], ['alt' => $achievement['title']]); ?>
                                <?php endif; ?>

                                <span class="file-name"><?= $achievement['title']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h1>Наши достижения еще не загружены.</h1>
                <?php endif; ?>
            </div>
            <?= $this->render('@app/views/_parts/right_bar'); ?>
        </div>
    </div>
</div>

