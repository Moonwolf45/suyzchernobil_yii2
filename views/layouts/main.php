<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\assets\FancyboxAsset;
use app\widgets\Alert;
use app\widgets\CategoryWidget;
use app\widgets\LastModifiedNewsWidget;
use yii\bootstrap5\Html;
use yii\helpers\Url;
use yii\widgets\Menu;

AppAsset::register($this);
FancyboxAsset::register($this);

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
$this->registerMetaTag(['name' => 'yandex-verification', 'content' => '43733762e6de7f10']);
?>

<?php $this->beginPage(); ?>
<!DOCTYPE html>
<html prefix="og: https://ogp.me/ns#" lang="<?= Yii::$app->language; ?>">
<head>
    <?php $this->head(); ?>
    <title><?= Html::encode($this->title); ?></title>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-54RTCGV42L"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-54RTCGV42L');
    </script>
    <!-- /Google tag (gtag.js) -->

    <!-- Yandex.Metrika counter -->
    <script type="text/javascript" >
        (function(m,e,t,r,i,k,a){m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)})
        (window, document, "script", "https://mc.yandex.ru/metrika/tag.js", "ym");

        ym(93541891, "init", {
            clickmap:true,
            trackLinks:true,
            accurateTrackBounce:true,
            webvisor:true,
            trackHash:true
        });
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/93541891" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>

<body>
<?php $this->beginBody(); ?>

<div class="container-fluid">
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-8 d-flex align-self-center fh5co_padding_menu">
                <?= Html::img('@web/images/logo.png', ['class' => 'fh5co_logo_width', 'alt' => 'img']); ?>
                <div class="d-inline-flex text-center fh5co_logo_font">
                    Курганская региональная<br>
                    общественная организация инвалидов<br>
                    <span>Союз "Чернобыль"</span>
                </div>
            </div>
            <div class="col-12 col-md-4 align-self-center fh5co_mediya_right">
                <div class="text-center d-inline-block">
                    <a href="<?= Url::to(['news/search']); ?>" class="fh5co_display_table">
                        <div class="fh5co_verticle_middle">
                            <i class="fa fa-search"></i>
                        </div>
                    </a>
                </div>
                <div class="text-center d-inline-block">
                    <a href="https://vk.com/club216053475" target="_blank" class="fh5co_display_table" title="Vk">
                        <div class="fh5co_verticle_middle">
                            <i class="fa fa-vk"></i>
                        </div>
                    </a>
                </div>
                <div class="text-center d-inline-block">
                    <a href="https://wa.me/79125266546" target="_blank" class="fh5co_display_table" title="Whatsapp">
                        <div class="fh5co_verticle_middle">
                            <i class="fa fa-whatsapp"></i>
                        </div>
                    </a>
                </div>
                <div class="text-center d-inline-block">
                    <a href="viber://chat?number=%2B79125266546" target="_blank" class="fh5co_display_table" title="Viber">
                        <div class="fh5co_verticle_middle">
                            <i class="fab fa-viber"></i>
                        </div>
                    </a>
                </div>
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid bg-faded fh5co_padd_mediya padding_786">
    <div class="container padding_786">
        <nav class="navbar navbar-toggleable-md navbar-light">
            <button class="navbar-toggler navbar-toggler-right mt-3" type="button" data-toggle="collapse" data-target="#navbarSupportedContent">
                <span class="fa fa-bars"></span>
            </button>
            <a class="navbar-brand" href="<?= Url::home(); ?>">
                <?= Html::img('@web/images/logo.png', ['alt' => 'img']); ?>
            </a>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <?= Menu::widget([
                    'items' => [
                        ['label' => 'Главная', 'url' => ['/news/index']],
                        ['label' => 'Архив', 'url' => ['/news/archive']],
                        ['label' => 'Книга памяти', 'url' => ['/news/documents']],
                        ['label' => 'Наши достижения', 'url' => ['/news/our-achievements']],
                        ['label' => 'Контакты', 'url' => ['/news/contact']],
                    ],
                    'activateParents' => true,
                    'activeCssClass' => 'active',
                    'options' => [
                        'class' => 'navbar-nav mr-auto'
                    ],
                    'itemOptions' => [
                        'class' => 'nav-item',
                    ],
                    'linkTemplate' => '<a class="nav-link" href="{url}">{label} <span class="sr-only">(current)</span></a>'
                ]); ?>
            </div>
        </nav>
    </div>
</div>

<?php if (Yii::$app->session->getFlash('subscribe')): ?>
    <div class="container-fluid bg-faded fh5co_padd_mediya padding_786">
        <div class="container padding_786">
            <?= Alert::widget(['key' => 'subscribe']); ?>
        </div>
    </div>
<?php endif; ?>

<?= $content; ?>

<div class="container-fluid fh5co_footer_bg pb-3">
    <div class="container animate-box">
        <div class="row">
            <div class="col-12 spdp_right py-5">
                <?= Html::img('@web/images/logo_white.png', ['class' => 'footer_logo', 'alt' => 'Белый лого']); ?>
            </div>
            <div class="clearfix"></div>
            <div class="col-12 col-md-4 col-lg-3">
                <div class="footer_main_title py-3">О нас</div>
                <div class="footer_sub_about pb-3">КРО СЧ – защита прав и законных интересов пострадавших от радиации
                    и членов их семей.</div>
                <div class="footer_mediya_icon">
                    <div class="text-center d-inline-block">
                        <a href="https://vk.com/club216053475" target="_blank" class="fh5co_display_table_footer"
                            title="Vk">
                            <div class="fh5co_verticle_middle">
                                <i class="fa fa-vk"></i>
                            </div>
                        </a>
                    </div>
                    <div class="text-center d-inline-block">
                        <a href="https://wa.me/79125266546" target="_blank" class="fh5co_display_table_footer"
                            title="Whatsapp">
                            <div class="fh5co_verticle_middle">
                                <i class="fa fa-whatsapp"></i>
                            </div>
                        </a>
                    </div>
                    <div class="text-center d-inline-block">
                        <a href="viber://chat?number=%2B79125266546" target="_blank" class="fh5co_display_table_footer"
                            title="Viber">
                            <div class="fh5co_verticle_middle">
                                <i class="fab fa-viber"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <?= CategoryWidget::widget(); ?>
            <?= LastModifiedNewsWidget::widget(); ?>
        </div>
        <div class="row justify-content-center pt-2 pb-4">
            <div class="col-12 col-md-8 col-lg-7">
                <form action="<?= Url::to(['/news/subscribe']);?>" method="post">
                    <?= Html::hiddenInput(Yii::$app->getRequest()->csrfParam, Yii::$app->getRequest()->getCsrfToken()); ?>
                    <div class="input-group">
                        <span class="input-group-addon fh5co_footer_text_box" id="basic-addon1">
                            <i class="fa fa-envelope"></i>
                        </span>
                        <input type="text" name="email" class="form-control fh5co_footer_text_box"
                               placeholder="Введите свой e-mail..." aria-describedby="basic-addon1">
                        <button type="submit" class="input-group-addon fh5co_footer_subcribe" id="basic-addon12">
                            <i class="fa fa-paper-plane-o"></i> Подписаться
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container-fluid fh5co_footer_right_reserved"></div>

<div class="gototop js-top">
    <a href="#" class="js-goTop"><i class="fa fa-arrow-up"></i></a>
</div>

<?php $this->endBody(); ?>
</body>
</html>
<?php $this->endPage(); ?>
