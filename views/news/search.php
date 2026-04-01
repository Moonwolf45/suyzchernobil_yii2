<?php

/** @var app\models\News $news */
/** @var yii\data\Pagination $pages */
/** @var string $q */
/** @var ActiveForm $model */

use app\widgets\BreadcrumbsSchemaWidget;
use yii\bootstrap5\ActiveForm;
use yii\bootstrap5\Html;
use yii\bootstrap5\LinkPager;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = 'Поиск: ' . $q;
?>
<div class="container-fluid pb-4 pt-4 paddding">
    <div class="container paddding">
        <?php if (!empty($news)): ?>
            <div class="row mx-0">
                <?= BreadcrumbsSchemaWidget::widget([
                    'links' => $this->params['breadcrumbs'] ?? [],
                ]); ?>
            </div>
            <div class="row mx-0">
                <div class="col-md-8 animate-box" itemscope itemtype="https://schema.org/WebSite" data-animate-effect="fadeInLeft">
                    <meta itemprop="url" content="<?= Url::current([], true); ?>"/>
                    <?php $form = ActiveForm::begin(['method' => 'get', 'options' => ['itemscope' => true,
                        'itemprop' => 'potentialAction', 'itemtype' => 'https://schema.org/SearchAction']]); ?>
                        <meta itemprop="target" content="<?= Url::current(['q' => $q], true); ?>"/>
                        <div class="input-group mb-3">
                            <input itemprop="query-input" type="text" class="form-control" name="q"
                                   placeholder="Введите запрос для поиска" value="<?= $q; ?>">
                            <?= Html::submitButton('Поиск', ['class' => 'btn btn-outline-secondary']); ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                    <div>
                        <div class="fh5co_heading fh5co_heading_border_bottom py-2 mb-4">
                            Поиск: <?= $q; ?>
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
        <?php else: ?>
            <div class="row mx-0">
                <div class="col-md-8 animate-box" itemscope itemtype="https://schema.org/WebSite" data-animate-effect="fadeInLeft">
                    <meta itemprop="url" content="<?= Url::current([], true); ?>"/>
                    <?php $form = ActiveForm::begin(['method' => 'get', 'options' => ['itemscope' => true,
                        'itemprop' => 'potentialAction', 'itemtype' => 'https://schema.org/SearchAction']]); ?>
                        <meta itemprop="target" content="<?= Url::current(['q' => $q], true); ?>"/>
                        <div class="input-group mb-3">
                            <input itemprop="query-input" type="text" class="form-control" name="q"
                                   placeholder="Введите запрос для поиска" value="<?= $q; ?>">
                            <?= Html::submitButton('Поиск', ['class' => 'btn btn-outline-secondary']); ?>
                        </div>
                    <?php ActiveForm::end(); ?>
                    <h1>По данному запросу новостей не найдено.</h1>
                </div>
                <?= $this->render('@app/views/_parts/right_bar'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>
