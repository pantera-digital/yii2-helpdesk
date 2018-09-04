<?php

namespace pantera\helpdesk;

use pantera\helpdesk\models\CreateTicketForm;
use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use phpDocumentor\Reflection\Types\Self_;
use yii\base\BaseObject;
use Yii;
use yii\db\Expression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\UploadedFile;

class Service extends BaseObject {
    const TYPE_NEW_TICKET_NOTIFICATION = 'ticket';
    const TYPE_NEW_RESPONSE_NOTIFICATION = 'response';
    const TYPE_CLOSED_TICKET_NOTIFICATION = 'closed';

    /**
     * @param Tickets $ticket
     */
    public static function toggleImportant(Tickets $ticket) {
        $ticket->important = $ticket->important ? 0 : 1;
        $ticket->save();
    }

    /**
     * @return array|null|\yii\db\ActiveRecord[]
     */
    public static function getActiveTicketsForCurrentUser() {
        if(!Yii::$app->user->isGuest) {
            return Tickets::find()->where([
                'user_id' => Yii::$app->user->id
            ])->orderBy('last_message DESC')->all();
        }
        return null;
    }

    public static function getActiveTicketsForAdmin() {
        if(Yii::$app->user->can('admin')) {
            return Tickets::find()->orderBy('last_message DESC')->all();
        }
        return null;
    }

    public static function closeTicket(Tickets $ticket) {
       $ticket->status = Tickets::STATUS_CLOSED;
       if($ticket->save()) {
           self::sendMailNotificationByType(self::TYPE_CLOSED_TICKET_NOTIFICATION, $ticket);
           return true;
       }
       return false;
    }

    public static function createMessage(Tickets $ticket, TicketMessages $message) {
        $ticket->last_message = new Expression('NOW()');
        $ticket->save();
        $message->ticket_id = $ticket->id;
        $message->is_admin = Yii::$app->user->can('admin') ?: 0;
        if($message->save()) {
            $ticket->refresh();
            if(count($ticket->messages) > 1) {
                self::sendMailNotificationByType(self::TYPE_NEW_RESPONSE_NOTIFICATION, $ticket);
            }
            return true;
        }
        return false;
    }

    public static function createTicket($ticket, $message) {
        try {
            Yii::$app->db->beginTransaction();
            $ticket->status = Yii::$app->user->can('admin') ? Tickets::STATUS_UPDATED_BY_ADMIN : Tickets::STATUS_UPDATED_BY_USER;
            $ticket->user_id = Yii::$app->user->id;
            if($ticket->save()) {
                if(self::createMessage($ticket, $message, true)) {
                    Yii::$app->db->transaction->commit();
                    self::sendMailNotificationByType(self::TYPE_NEW_TICKET_NOTIFICATION, $ticket);
                    return true;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::$app->db->transaction->rollBack();
        }
    }

    public function sendMailNotificationByType(string $type, Tickets $ticket)  {
        /** @var Module $module */
        $module = Yii::$app->getModule('helpdesk');
        $preMessage =  Html::tag('h3','Добрый день.') . '<br>';
        $lastMessage = $ticket->getMessages()->orderBy('id DESC')->one();
        $ticketLink = Html::a('Обращение под номером ' . $ticket->id, 'http://' . $_SERVER['HTTP_HOST'] . '/helpdesk/default/view/' . $ticket->id);
        $adminMail = Yii::$app->params['adminEmail'];
        $message = '';
        $subject = '';
        $to = '';
        switch ($type):
            case self::TYPE_NEW_TICKET_NOTIFICATION:
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . $ticketLink . ' успешно зарегистрировано.',
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($ticket->email)
                    ->setSubject('Ваше обращение успешно зарегистрировано')
                    ->send();

                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . $ticketLink . ' было зарегистрировано в helpdesk.<br>' .
                        'Суть обращения: ' . $lastMessage->message,
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($adminMail)
                    ->setSubject('Новое обращение в helpdesk')
                    ->send();
                break;
            case self::TYPE_NEW_RESPONSE_NOTIFICATION:
                if($lastMessage->is_admin) {
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'На ' . strtolower($ticketLink) . ' ответили<br>' .
                        'Текст ответа:<br>' . $lastMessage->message,
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($ticket->email)
                    ->setSubject('На ваше обращение ответили')
                    ->send();
                } else {
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'На ' . strtolower($ticketLink) . ' ответили<br>' .
                            'Текст ответа:<br>' . $lastMessage->message,
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($adminMail)
                    ->setSubject('В helpdesk новый ответ в обращении')
                    ->send();
                }
                break;
            case self::TYPE_CLOSED_TICKET_NOTIFICATION:
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'Ваше ' . strtolower($ticketLink) . ' закрыто',
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($ticket->email)
                    ->setSubject('Ваше обращение закрыто.')
                    ->send();
                break;
        endswitch;

    }

}