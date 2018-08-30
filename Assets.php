<?php

namespace pantera\helpdesk;
use yii\web\AssetBundle;
use yii\web\JqueryAsset;
use yii\web\YiiAsset;

class Assets extends AssetBundle
{
    public $sourcePath = '@vendor/pantera-digital/yii2-helpdesk/resources';

    public $css = [
        'css/style.css',
    ];

    public $js = [
        'js/main.js',
        'https://www.google.com/recaptcha/api.js?onload=onloadCallback'
    ];

    public $depends = [
        JqueryAsset::class,
        YiiAsset::class,
    ];
}