<?php

namespace app\assets;


use yii\web\AssetBundle;

class FancyboxAsset extends AssetBundle {

    public $sourcePath = '@bower/fancybox/dist';
    public $css = [
        'jquery.fancybox.css',
    ];
    public $js = [
        'jquery.fancybox.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
    ];
}