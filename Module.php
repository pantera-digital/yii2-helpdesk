<?php

namespace pantera\helpdesk;

class Module extends \yii\base\Module
{
    public $accessRoles = ['@'];
    public $profileModel = null;
    public $nameAttribute = 'name';
    public $emailAttribute = 'email';
    public $googleCaptchaSiteKey = '';
    public $googleCaptchaSecret = '';
    public $mailNotificationView = 'notification';
    public $userClass = '\dektrium\user\models\User';
    public $frontendUrl = null;
    public $backendUrl = null;

    public function isAdmin()
    {
        return false;
    }
}
