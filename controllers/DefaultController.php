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
use pantera\helpdesk\models\Tickets;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;
use yii\filters\AccessControl;
use Yii;

class DefaultController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => is_array($accessRoles = Yii::$app->getModule('helpdesk')->accessRoles)
                            ? $accessRoles
                            : [$accessRoles],
                    ],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'file-upload-innostudio' => [
                'class' => \pantera\media\actions\kartik\MediaUploadActionKartik::class,
                'model' => function () {
                    if (Yii::$app->request->get('id')) {
                        return $this->findModel(Yii::$app->request->get('id'));
                    } else {
                        return new TicketMessages();
                    }
                },
            ],
            'file-delete-innostudio' => [
                'class' => \pantera\media\actions\kartik\MediaDeleteActionKartik::class,
                'model' => function () {
                    return \pantera\media\models\Media::findOne(Yii::$app->request->post('id'));
                },
            ],
            'create' => [
                'class' => CreateAction::class,
            ],
            'download' => [
                'class' => DownloadAction::class,
            ],
            'close' => [
                'class' => CloseAction::class,
            ],
        ];
    }

    public function actionIndex()
    {
        $model = new Tickets();
        if ((!is_null($this->module->profileModel)) && ($profile = $this->module->profileModel)) {
            $profile = $profile();
            if($profile) {
                $model->name = $profile->{$this->module->nameAttribute};
                $model->email = $profile->user->{$this->module->emailAttribute};
            }
        }
        return $this->render('index', [
            'model' => $model,
            'tickets' => Service::getActiveTicketsForCurrentUser(),
            'newMessage' => new TicketMessages()
        ]);
    }

    public function actionView($id)
    {
        $ticket = $this->findTicket($id);
        return $this->render('view',[
            'ticket' => $ticket,
            'userTickets' => Service::getActiveTicketsForCurrentUser(),
            'messages' => $ticket->getMessages()->orderBy('id DESC')->all(),
            'newMessage' => new TicketMessages(),
        ]);
    }

    public function findTicket($id)
    {
        if($ticket = Tickets::findOne($id)) {
            return $ticket;
        } else {
            throw new NotFoundHttpException();
        }
    }
}
