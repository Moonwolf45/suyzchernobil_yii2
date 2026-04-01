<?php

namespace app\assets;


use yii\web\AssetBundle;

class AdminLteAsset extends AssetBundle {

    public $sourcePath = '@vendor/almasaeed2010/adminlte/dist';
    public $css = [
        'css/adminlte.css',
    ];

    public $js = [
        'js/adminlte.js',
    ];

    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap5\BootstrapPluginAsset',
        'app\assets\FontAwesomeAsset'
    ];
}
