<?php

namespace pantera\helpdesk\admin;

use Yii;

class Module extends \pantera\helpdesk\Module
{
    public $accessRoles = ['admin'];

    public function isAdmin()
    {
        if (!is_array($this->accessRoles)) {
            return Yii::$app->user->can($this->accessRoles);

        } else {
            if (in_array('@', $this->accessRoles) && !Yii::$app->user->isGuest) {
                return true;
            }

            foreach ($this->accessRoles as $role) {
                if (Yii::$app->user->can($role)) {
                    return true;
                }
            }
        }

        return false;
    }
}
