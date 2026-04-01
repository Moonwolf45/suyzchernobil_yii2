<?php

/** @var app\models\News $news */
/** @var app\models\Category $category */
/** @var yii\data\Pagination $pages */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\bootstrap5\LinkPager;

$this->params['breadcrumbs'][] = $category['title'];
?>
<div class="container-fluid pb-4 pt-4 paddding">
    <?php if (!empty($news)): ?>
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
                            <?= $category['title']; ?>
                        </div>
                    </div>

                    <?php foreach ($news as $news_one): ?>
                        <?= $this->render('@app/views/_parts/news_item', ['news' => $news_one]); ?>
                    <?php endforeach; ?>
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
                    <h1>Новостей в данной категории не найдено.</h1>
                </div>
                <?= $this->render('@app/views/_parts/right_bar'); ?>
            </div>
        </div>
    <?php endif; ?>
</div>
