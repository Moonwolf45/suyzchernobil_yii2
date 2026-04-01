<?php

/** @var app\models\Documents $documents */
/** @var yii\data\Pagination $pages */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\helpers\Html;

$this->params['breadcrumbs'][] = ['label' => 'Книга памяти'];
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
                        Книга памяти
                    </div>
                </div>

                <?php if (!empty($documents)): ?>
                    <div class="news-gallery-block">
                        <?php foreach ($documents as $document): ?>
                            <?php $isPdf = $document['isPdf']; ?>
                            <a class="news-gallery" <?= $isPdf ?: 'data-fancybox="gallery"'; ?> href="<?= '/' . $document['file']; ?>"
                                data-caption="<?= $document['title']; ?>">

                                <?php if ($document['fasten']): ?>
                                    <span class="image-fasten">Закреплён</span>
                                <?php endif; ?>

                                <?php if ($isPdf): ?>
                                    <?php if ($document['image'] !== null): ?>
                                        <?= Html::img('@web/' . $document['image'], ['alt' => $document['title']]); ?>
                                    <?php else: ?>
                                        <?= Html::img('@web/images/pdf.png', ['alt' => $document['title']]); ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= Html::img('@web/' . $document['file'], ['alt' => $document['title']]); ?>
                                <?php endif; ?>

                                <span class="file-name"><?= $document['title']; ?></span>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <h1>Документы еще не загружены.</h1>
                <?php endif; ?>
            </div>
            <?= $this->render('@app/views/_parts/right_bar'); ?>
        </div>
    </div>
</div>

