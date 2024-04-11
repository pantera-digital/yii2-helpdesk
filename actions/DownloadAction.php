<?php

namespace pantera\helpdesk\actions;

use pantera\helpdesk\models\TicketMessages;
use pantera\media\models\Media;
use yii\web\NotFoundHttpException;
use Yii;

class DownloadAction extends \yii\base\Action
{
    public function run($id)
    {
        if (
            ($model = Media::findOne($id))
            && ($ticketMessage = TicketMessages::findOne($model->model_id))
            && ($ticket = $ticketMessage->ticket)
            && ($ticket->user_id == Yii::$app->user->id || Yii::$app->getModule('helpdesk')->isAdmin())
        ) {
            return Yii::$app->response->sendFile($model->getPath());
        }
        throw new NotFoundHttpException();
    }
}
