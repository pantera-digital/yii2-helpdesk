<?php

namespace pantera\helpdesk\actions;

use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use pantera\helpdesk\Service;
use yii\base\Action;
use Yii;
use yii\helpers\Json;

class CreateAction extends Action {
    public function run($id = null) {
        if(Yii::$app->user->isGuest) {
            $postdata = http_build_query([
                'secret' => $this->controller->module->googleCaptchaSecret,
                'response' => Yii::$app->request->post('g-recaptcha-response')
            ]);
            $opts = array('http' =>
                [
                    'method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded',
                    'content' => $postdata
                ]
            );
            $context = stream_context_create($opts);
            $result = file_get_contents('https://www.google.com/recaptcha/api/siteverify', false, $context);
            $data = Json::decode($result);
        }
        $ticket = new Tickets();
        $message = new TicketMessages();
        $params = Yii::$app->request->post();
        $ticket->load($params);
        $message->load($params);
        if(!Yii::$app->user->isGuest || $data['success']) {
            if(is_null($id) && Service::createTicket($ticket, $message)) {
                if(!Yii::$app->request->isAjax) {
                    Yii::$app->session->setFlash('success', 'Тикет успешно создан');
                    $this->controller->redirect(Yii::$app->request->referrer);
                } else {
                    $this->controller->asJson(['status' => 'success']);
                }
            } elseif(!is_null($id)) {
                $ticket = $this->controller->findTicket($id);
                if(Service::createMessage($ticket, $message)) {
                    if(!Yii::$app->request->isAjax) {
                        Yii::$app->session->setFlash('success', 'Сообщение успешно отправлено');
                        $this->controller->redirect(Yii::$app->request->referrer);
                    } else {
                        $this->controller->asJson(['status' => 'success']);
                    }
                }
            }
        }
        $this->controller->redirect(Yii::$app->request->referrer);
    }
}