<?php

namespace pantera\helpdesk\actions;

use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use pantera\helpdesk\Service;
use pantera\media\models\Media;
use yii\base\Action;
use Yii;

class ImportantAction extends Action {

    public function run($id) {
        if($ticket = $this->controller->findTicket($id)) {
            if(Service::toggleImportant($ticket)) {
                Yii::$app->session->setFlash('success','Тикет ' . $ticket->id . ' помечен как важный');
            }
        }
        $this->controller->redirect(['index']);
    }
}