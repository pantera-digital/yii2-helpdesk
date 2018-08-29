<?php

namespace pantera\helpdesk;

class Module extends \yii\base\Module {
    public $profileModel = null;
    public $nameAttribute = 'name';
    public $emailAttribute = 'email';
    public $googleCaptchaSiteKey = '';
    public $fileUploadDir = '';
    public $mailNotificationView = 'notification';
}
