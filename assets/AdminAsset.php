<?php

namespace app\assets;


use yii\web\AssetBundle;


class AdminAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';

    public $css = [
        'css/admin/style.css'
    ];

    public $js = [
        'js/admin/demo.js'
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapAsset',
        'app\assets\FontAwesomeAsset',
        'app\assets\AdminLteAsset'
    ];
}
