# yii2-helpdesk
NOTICE: This module depends on pantera-digital/yii2-media

Install
---------------------------------
Run

```
php composer require pantera-digital/yii2-helpdesk "*"
```

Or add to composer.json

```
"pantera-digital/yii2-helpdesk": "*",
```

and execute:

```
php composer update
```
Configure your app :
```php
 [
    'bootstrap' => [... ,'media'],
    'modules' => [
        ...
        'media' => [
            'class' => pantera\media\Module::className(),
            'permissions' => ['admin'], //permissions for administrate media module
            'mediaUrlAlias' => 'path for url access to media files',
            'mediaFileAlias' => 'path for storage media files',
            'tableName' => 'pantera_media', //table name for media 
        ],
        'helpdesk' => [
            'class' => \pantera\helpdesk\Module::class, 
            'profileModel' => function() { //here u can catch the current user profile model
                if(!Yii::$app->user->isGuest) {
                    return Yii::$app->user->identity->profile; 
                } else {
                    return null;
                }
            },
            'nameAttribute' => 'HERE NAME ATTRIBUTE OF USER PROFILE MODEL',
            'emailAttribute' => 'HERE EMAIL ATTRIBUTE OF USER MODEL',
            'googleCaptchaSiteKey' => 'here your google recaptcha site key',
            'googleCaptchaSecret' => 'here your google recaptcha secret',
            'mailNotificationView' => 'here you can set the path to your mail view like @app/mail/view_name',
            'userClass' => 'this is a class for your user module by default it dependency on \dektrium\user\models\User';
        ],  
    ],
    ...
];
```
Configure your admin app:
```php
 'helpdesk' => [
            'class' => \pantera\helpdesk\Module::class,
            'viewPath' => '@vendor/pantera-digital/yii2-helpdesk/admin/views',
            'controllerNamespace' => 'pantera\helpdesk\admin\controllers'
 ],
```

Add to your console config:
```php
 ...
 'controllerMap' => [
        'migrate' => [
            'class' => MigrateController::className(),
            'migrationPath' => [
                ....
                '@vendor/pantera-digital/yii2-helpdesk/migrations',
                '@pantera/media/migrations',
                ....
            ],
        ],
 ... 
```

or run migrations:

```
php yii migrate --migrationPath=vendor/pantera-digital/yii2-media/migrations
php yii migrate --migrationPath=vendor/pantera-digital/yii2-helpdesk/migrations
```

Available actions:

######For admin:
```
helpdesk/default/index
helpdesk/default/view
```
######For user: 
```
helpdesk/default/index
helpdesk/default/view
```
######For no-auth user:
```
helpdesk/defaul/index
```
###NOTICE: Tested only on yii2 advanced application

