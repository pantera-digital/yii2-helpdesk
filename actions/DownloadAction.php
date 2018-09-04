<?php

namespace pantera\helpdesk\actions;

use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use pantera\helpdesk\Service;
use pantera\media\models\Media;
use yii\base\Action;
use Yii;

class DownloadAction extends Action {

    public function run($id) {
        $model = Media::findOne($id);
        $ticket = Tickets::findOne($model->model_id);
        if (!Yii::$app->user->can('admin')) {
            if ($ticket->user_id !== Yii::$app->user->id) {
                throw new NotFoundHttpException();
            }
        }
        Yii::$app->response->sendFile($model->getPath());
    }
}