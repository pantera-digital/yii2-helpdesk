<?php

namespace pantera\helpdesk;
use yii\web\AssetBundle;

class Assets extends AssetBundle
{
    public $sourcePath = '@vendor/pantera-digital/yii2-helpdesk/resources';
    public $css = [
        'css/style.css',
    ];
}