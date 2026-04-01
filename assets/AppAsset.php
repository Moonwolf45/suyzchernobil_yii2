<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle {

    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/bootstrap.css',
        'css/animate.css',
        'css/owl.carousel.css',
        'css/owl.theme.default.css',
        'css/style.css',
        'css/media_query.css'
    ];

    public $js = [
        'js/modernizr-3.5.0.min.js',
        'js/owl.carousel.min.js',
        'js/tether.min.js',
        'js/bootstrap.js',
        'js/jquery.waypoints.min.js',
        'js/jquery-migrate-3.4.1.min.js',
        'js/jquery.stellar.js',
        'js/main.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\FontAwesomeAsset',
    ];
}
