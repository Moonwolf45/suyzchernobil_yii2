<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AdminAsset;
use app\widgets\AdminMenuWidget;
use app\widgets\Alert;
use app\widgets\BreadcrumbsSchemaWidget;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

AdminAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['http-equiv' => 'Content-Type', 'content' => 'text/html; charset=' .  Yii::$app->charset]);
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['http-equiv' => 'X-UA-Compatible', 'content' => 'IE=edge']);

$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '57x57', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-57x57.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '60x60', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-60x60.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '72x72', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-72x72.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '76x76', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-76x76.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '114x114', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-114x114.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '120x120', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-120x120.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '144x144', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-144x144.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '152x152', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-152x152.png')]);
$this->registerLinkTag(['rel' => 'apple-touch-icon', 'sizes' => '180x180', 'href' => Yii::getAlias('@web/images/favicon/apple-icon-180x180.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '192x192', 'href' => Yii::getAlias('@web/images/favicon/android-icon-192x192.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '32x32', 'href' => Yii::getAlias('@web/images/favicon/favicon-32x32.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '96x96', 'href' => Yii::getAlias('@web/images/favicon/favicon-96x96.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/png', 'sizes' => '16x16', 'href' => Yii::getAlias('@web/images/favicon/favicon-16x16.png')]);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/images/favicon/favicon.ico')]);
$this->registerLinkTag(['rel' => 'shortcut icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/images/favicon/favicon.ico')]);
$this->registerLinkTag(['rel' => 'manifest', 'href' => Yii::getAlias('@web/images/favicon/manifest.json')]);
$this->registerLinkTag(['name' => 'msapplication-TileColor', 'content' => '#ffffff']);
$this->registerLinkTag(['name' => 'msapplication-TileImage', 'content' => Yii::getAlias('@web/images/favicon/ms-icon-144x144.png')]);
$this->registerLinkTag(['name' => 'theme-color', 'content' => '#ffffff']);
?>

<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language; ?>">
<head>
    <?php $this->head(); ?>
    <title>Админка Союз Чернобыль - Курган</title>
</head>

<body class="hold-transition sidebar-mini layout-fixed">
<?php $this->beginBody(); ?>
<div class="wrapper">
    <div class="preloader flex-column justify-content-center align-items-center">
        <?= Html::img('@web/images/admin/AdminLTELogo.png', ['class' => 'animation__shake', 'alt' => 'AdminLTELogo',
            'height' => 60, 'width' => 60]); ?>
    </div>

    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <?= Menu::widget([
            'items' => [
                [
                    'label' => '<i class="fas fa-bars"></i>',
                    'options' => ['data-widget' => 'pushmenu', 'role' => 'button'],
                    'url' => false,
                    'encode' => false
                ], [
                    'label' => 'Категории',
                    'url' => ['/admin/category/index']
                ], [
                    'label' => 'Теги',
                    'url' => ['/admin/tags/index']
                ], [
                    'label' => 'Новости',
                    'url' => ['/admin/news/index']
                ], [
                    'label' => 'Доп. фотографии новостей',
                    'url' => ['/admin/news-image/index']
                ], [
                    'label' => 'Видео-новости',
                    'url' => ['/admin/news-video/index']
                ], [
                    'label' => 'Документы',
                    'url' => ['/admin/documents/index']
                ], [
                    'label' => 'Достижения',
                    'url' => ['/admin/achievements/index']
                ], [
                    'label' => 'Пользователи',
                    'url' => ['/admin/users/index']
                ], [
                    'label' => 'Подписки',
                    'url' => ['/admin/subscribe/index']
                ], [
                    'label' => 'Выход',
                    'url' => ['/news/logout']
                ]
            ],
            'activateParents' => true,
            'activeCssClass' => 'active',
            'options' => [
                'class' => 'navbar-nav'
            ],
            'itemOptions' => [
                'class' => 'nav-item',
            ],
            'linkTemplate' => '<a class="nav-link" href="{url}">{label} <span class="sr-only">(current)</span></a>'
        ]); ?>
    </nav>

    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= Url::home(); ?>" class="brand-link">
            <?= Html::img('@web/images/admin/AdminLTELogo.png', ['class' => 'brand-image img-circle elevation-3', 'alt' => 'AdminLTELogo',
                'style' => 'opacity: .8', 'width' => 60]); ?>
            <span class="brand-text font-weight-light">AdminLTE 3</span>
        </a>

        <div class="sidebar">
            <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                <div class="image">
                    <?= Html::img('@web/images/admin/avatar.png', ['class' => 'img-circle elevation-2', 'alt' => 'Admin Image']); ?>
                </div>
                <div class="info">
                    <a href="#" class="d-block">Администратор</a>
                </div>
            </div>

            <!-- SidebarSearch Form -->
            <div class="form-inline">
                <div class="input-group" data-widget="sidebar-search">
                    <input class="form-control form-control-sidebar" type="search" placeholder="Поиск" aria-label="Поиск">
                    <div class="input-group-append">
                        <button class="btn btn-sidebar">
                            <i class="fas fa-search fa-fw"></i>
                        </button>
                    </div>
                </div>
            </div>

            <nav class="mt-2">
                <?= AdminMenuWidget::widget([
                    'items' => [
                        [
                            'label' => 'Категории',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все категории', 'url' => ['/admin/category/index']],
                                ['label' => 'Создать категорию', 'url' => ['/admin/category/create']],
                            ]
                        ], [
                            'label' => 'Теги',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все теги', 'url' => ['/admin/tags/index']],
                                ['label' => 'Создать тег', 'url' => ['/admin/tags/create']],
                            ]
                        ], [
                            'label' => 'Новости',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все новости', 'url' => ['/admin/news/index']],
                                ['label' => 'Создать новость', 'url' => ['/admin/news/create']],
                            ]
                        ], [
                            'label' => 'Доп. фотографии новостей',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все доп. фотографии новостей', 'url' => ['/admin/news-image/index']],
                                ['label' => 'Создать доп. фотографию', 'url' => ['/admin/news-image/create']],
                            ]
                        ], [
                            'label' => 'Видео-новости',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все видео-новости', 'url' => ['/admin/news-video/index']],
                                ['label' => 'Создать видео-новость', 'url' => ['/admin/news-video/create']],
                            ]
                        ], [
                            'label' => 'Документы',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все документы', 'url' => ['/admin/documents/index']],
                                ['label' => 'Загрузить документ', 'url' => ['/admin/documents/create']],
                            ]
                        ], [
                            'label' => 'Достижения',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все достижения', 'url' => ['/admin/achievements/index']],
                                ['label' => 'Загрузить достижение', 'url' => ['/admin/achievements/create']],
                            ]
                        ], [
                            'label' => 'Пользователи',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все пользователи', 'url' => ['/admin/users/index']],
//                                ['label' => 'Создать пользователя', 'url' => ['/admin/users/create']],
                            ]
                        ], [
                            'label' => 'Подписки',
                            'url' => false,
                            'items' => [
                                ['label' => 'Все подписки', 'url' => ['/admin/subscribe/index']],
//                                ['label' => 'Создать подписку', 'url' => ['/admin/subscribe/create']],
                            ]
                        ]
                    ],
                    'activateParents' => true,
                    'activeCssClass' => 'active',
                    'options' => [
                        'class' => 'nav nav-pills nav-sidebar flex-column',
                        'data-widget' => 'treeview',
                        'role' => 'menu',
                        'data-accordion' => 'false',
                    ],
                    'itemOptions' => [
                        'class' => 'nav-item',
                    ],
                    'submenuTemplate' => '<ul class="nav nav-treeview">{items}</ul>'
                ]); ?>
            </nav>
        </div>
    </aside>

    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= Html::encode($this->title); ?></h1>
                    </div>
                    <div class="col-sm-6">
                        <?= BreadcrumbsSchemaWidget::widget([
                            'homeLinkUrl' => Url::home() . 'admin',
                            'links' => $this->params['breadcrumbs'] ?? [],
                            'navOptions' => ['class' => 'breadcrumb float-sm-right']
                        ]); ?>
                    </div>
                </div>
                <div class="row mb-2">
                    <?= Alert::widget(['key' => ['category', 'tags', 'documents', 'achievements', 'news', 'news-image', 'news-video']]); ?>
                </div>
            </div>
        </div>

        <section class="content">
            <div class="container-fluid">
                <div class="row">
                    <?= $content; ?>
                </div>
            </div>
        </section>
    </div>

    <footer class="main-footer">
        <strong>Copyright &copy; 2023.</strong>
        All rights reserved.
    </footer>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
