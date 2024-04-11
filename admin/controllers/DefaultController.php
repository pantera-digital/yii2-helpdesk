<?php

namespace pantera\helpdesk\admin\controllers;

use pantera\helpdesk\actions\CloseAction;
use pantera\helpdesk\actions\CreateAction;
use pantera\helpdesk\actions\DownloadAction;
use pantera\helpdesk\actions\ImportantAction;
use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use pantera\helpdesk\models\TicketsSearch;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
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
            'create' => [
                'class' => CreateAction::class,
            ],
            'download' => [
                'class' => DownloadAction::class,
            ],
            'close' => [
                'class' => CloseAction::class,
            ],
            'important' => [
                'class' => ImportantAction::class,
            ],
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
        ];
    }

    public function actionIndex()
    {
        $searchModel = new TicketsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->get());
        
        return Yii::$app->request->isAjax
            ? $this->renderAjax('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ])
            : $this->render('index', [
                'dataProvider' => $dataProvider,
                'searchModel' => $searchModel,
            ]);
    }

    public function actionComment($id)
    {
        $ticket = $this->findTicket($id);
        $ticket->load(Yii::$app->request->post());
        if ($ticket->save()) {
            Yii::$app->session->setFlash('success', 'Комментарий успешно добавлен');
            $this->redirect(['index']);
        }
    }

    public function actionAllMessages($id, $limit = 1000)
    {
        $ticket = $this->findTicket($id);
        $messages = $ticket->getMessages()
            ->orderBy('id ' . ($limit == 1000 ? 'ASC' : 'DESC'))
            ->limit($limit)
            ->all();
        return $this->renderAjax('_messages', [
            'messages' => $limit == 1000 ? $messages : array_reverse($messages),
        ]);
    }

    public function findTicket($id)
    {        
        if ($ticket = Tickets::findOne($id)) {
            return $ticket;
        } else {
            throw new NotFoundHttpException();
        }
    }
}
