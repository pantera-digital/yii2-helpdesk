<?php

namespace pantera\helpdesk\controllers;
use pantera\helpdesk\actions\CloseAction;
use pantera\helpdesk\actions\CreateAction;
use pantera\helpdesk\actions\DownloadAction;
use pantera\helpdesk\models\CreateTicketForm;
use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\Module;
use pantera\helpdesk\Service;
use pantera\media\models\Media;
use Yii;
use pantera\helpdesk\models\Tickets;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Class DefaultController
 * @package pantera\helpdesk
 * @property Module $module
 */
class DefaultController extends Controller {

    public function actions()
    {
        return [
            'file-upload-innostudio' => [
                'class' => \pantera\media\actions\kartik\MediaUploadActionKartik::className(),
                'model' => function () {
                    if (Yii::$app->request->get('id')) {
                        return $this->findModel(Yii::$app->request->get('id'));
                    } else {
                        return new TicketMessages();
                    }
                }
            ],
            'file-delete-innostudio' => [
                'class' => \pantera\media\actions\kartik\MediaDeleteActionKartik::className(),
                'model' => function () {
                    return \pantera\media\models\Media::findOne(Yii::$app->request->post('id'));
                }
            ],
            'create' => [
                'class' => CreateAction::class,
            ],
            'download' => [
                'class' => DownloadAction::class,
            ],
            'close' => [
                'class' => CloseAction::class
            ]
        ];
    }

    /**
     * @return string
     */
    public function actionIndex() {
        $model = new Tickets();
        if((!is_null($this->module->profileModel)) && ($profile = $this->module->profileModel)) { //Профиль пользователя определен
            $profile = $profile();
            if($profile) {
                $model->name = $profile->{$this->module->nameAttribute};
                $model->email = $profile->user->{$this->module->emailAttribute};
            }
        }
        return $this->render('index', [
            'model' => $model,
            'tickets' => Service::getActiveTicketsForCurrentUser(),
            'ticketsForAdmin' => Service::getActiveTicketsForAdmin(),
            'newMessage' => new TicketMessages()
        ]);
    }

    public function actionView($id) {
        $ticket = $this->findTicket($id);
        return $this->render('view',[
            'ticket' => $ticket,
            'userTickets' => Service::getActiveTicketsForCurrentUser(),
            'messages' => $ticket->getMessages()->orderBy('id DESC')->all(),
            'newMessage' => new TicketMessages(),
        ]);
    }



    public function findTicket($id) {
        if(Yii::$app->user->can('admin')) {
            $criteria = ['id' => $id];
        } else {
            $criteria = ['id' => $id, 'user_id' => Yii::$app->user->id];
        }
        if($ticket = Tickets::findOne($criteria)) {
            return $ticket;
        } else {
            throw new NotFoundHttpException();
        }
    }
}
