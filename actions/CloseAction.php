<?php

namespace pantera\helpdesk\actions;

use pantera\helpdesk\Service;
use yii\base\Action;
use Yii;

class CloseAction extends Action
{
    public function run($id)
    {
        if ($ticket = $this->controller->findTicket($id)) {
            if (Service::closeTicket($ticket)) {
                Yii::$app->session->setFlash('success','Тикет успешно закрыт');
            }
        }
        $this->controller->redirect(['index']);
    }
}
