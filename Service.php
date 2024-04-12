<?php

namespace pantera\helpdesk;

use pantera\helpdesk\models\TicketMessages;
use pantera\helpdesk\models\Tickets;
use yii\helpers\Html;
use yii\db\Expression;
use Yii;

class Service extends \yii\base\BaseObject
{
    const TYPE_NEW_TICKET_NOTIFICATION = 'ticket';
    const TYPE_NEW_RESPONSE_NOTIFICATION = 'response';
    const TYPE_CLOSED_TICKET_NOTIFICATION = 'closed';

    public static function toggleImportant(Tickets $ticket)
    {
        $ticket->important = (int)!$ticket->important;
        $ticket->save();
    }

    public static function getActiveTicketsForCurrentUser()
    {
        if (!Yii::$app->user->isGuest) {
            return Tickets::find()
                ->where(['user_id' => Yii::$app->user->id])
                ->orderBy('last_message DESC')
                ->all();
        }
        return null;
    }

    public static function closeTicket(Tickets $ticket)
    {
       $ticket->status = Tickets::STATUS_CLOSED;
       if ($ticket->save()) {
           (new self)->sendMailNotificationByType(self::TYPE_CLOSED_TICKET_NOTIFICATION, $ticket);
           return true;
       }
       return false;
    }

    public static function createMessage(Tickets $ticket, TicketMessages $message)
    {
        $ticket->last_message = new Expression('NOW()');
        $ticket->status = Yii::$app->getModule('helpdesk')->isAdmin()
            ? Tickets::STATUS_UPDATED_BY_ADMIN
            : Tickets::STATUS_UPDATED_BY_USER;
        $ticket->save();
        $message->ticket_id = $ticket->id;
        $message->is_admin = (int)Yii::$app->getModule('helpdesk')->isAdmin();
        if ($message->save()) {
            $ticket->refresh();
            if (count($ticket->messages) > 1) {
                (new self)->sendMailNotificationByType(self::TYPE_NEW_RESPONSE_NOTIFICATION, $ticket);
            }
            return true;
        }
        return false;
    }

    public static function createTicket($ticket, $message)
    {
        try {
            Yii::$app->db->beginTransaction();
            $ticket->status = Yii::$app->getModule('helpdesk')->isAdmin()
                ? Tickets::STATUS_UPDATED_BY_ADMIN
                : Tickets::STATUS_UPDATED_BY_USER;
            $ticket->user_id = Yii::$app->user->id;
            if ($ticket->save()) {
                if (self::createMessage($ticket, $message, true)) {
                    Yii::$app->db->transaction->commit();
                    (new self)->sendMailNotificationByType(self::TYPE_NEW_TICKET_NOTIFICATION, $ticket);
                    return true;
                }
            }
        } catch (Exception $e) {
            echo $e->getMessage();
            Yii::$app->db->transaction->rollBack();
        }
    }

    public function sendMailNotificationByType(string $type, Tickets $ticket)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('helpdesk');

        $frontendUrl = $module->frontendUrl ?: 'https://' . $_SERVER['HTTP_HOST'];
        $backendUrl = $module->backendUrl ?: 'https://' . $_SERVER['HTTP_HOST'];

        $preMessage =  Html::tag('h3','Добрый день.') . '<br>';

        $lastMessage = $ticket->getMessages()->orderBy('id DESC')->one();

        $ticketLink = Html::a('Обращение под номером ' . $ticket->id, $frontendUrl . '/helpdesk/default/view/' . $ticket->id);
        $adminTicketLink = Html::a('Обращение под номером ' . $ticket->id, $backendUrl . '/helpdesk');

        $adminMail = Yii::$app->params['adminEmail'];

        $message = '';
        $subject = '';
        $to = '';

        $replyTo = $ticket->email ?? $ticket->user->{$module->emailAttribute};

        switch ($type):
            case self::TYPE_NEW_TICKET_NOTIFICATION:
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . $ticketLink . ' успешно зарегистрировано.',
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setReplyTo(Yii::$app->user->identity->email ?? Yii::$app->params['adminEmail'])
                    ->setTo($ticket->email)
                    ->setSubject('Ваше обращение успешно зарегистрировано.')
                    ->send();

                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . $adminTicketLink . ' было зарегистрировано в helpdesk.<br>' .
                        'Суть обращения: <br>' . $lastMessage->message,
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setReplyTo($replyTo)
                    ->setTo($adminMail)
                    ->setSubject($ticket->subject)
                    ->send();
                break;
            case self::TYPE_NEW_RESPONSE_NOTIFICATION:
                if($lastMessage->is_admin) {
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'На ' . strtolower($ticketLink) . ' ответили.<br>' .
                        'Текст ответа:<br>' . $lastMessage->message,
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setReplyTo($replyTo)
                    ->setTo($ticket->email)
                    ->setSubject('На ваше обращение ответили.')
                    ->send();
                } else {
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'На ' . strtolower($ticketLink) . ' ответили.<br>' .
                            'Текст ответа:<br>' . $lastMessage->message,
                    ])
                    ->setReplyTo($ticket->email ?? $ticket->user->{$module->emailAttribute})
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($adminMail)
                    ->setSubject('В helpdesk новый ответ в обращении.')
                    ->send();
                }
                break;
            case self::TYPE_CLOSED_TICKET_NOTIFICATION:
                    Yii::$app->mailer->compose($module->mailNotificationView,[
                        'content' => $preMessage . 'Ваше ' . strtolower($ticketLink) . ' закрыто.',
                    ])
                    ->setFrom(Yii::$app->params['adminEmail'])
                    ->setTo($ticket->email)
                    ->setSubject('Ваше обращение закрыто.')
                    ->send();
                break;
        endswitch;
    }
}
