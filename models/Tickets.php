<?php

namespace pantera\helpdesk\models;

use pantera\helpdesk\Module;
use Yii;

/**
 * @property int $id
 * @property string $user_id User identity
 * @property string $subject Ticket theme
 * @property string $email User e-mail
 * @property string $name User name
 * @property int $status Ticket status
 * @property string $created_at
 * @property string $updated_at
 * @property TicketMessages[] $messages
 */
class Tickets extends \yii\db\ActiveRecord
{
    const STATUS_UPDATED_BY_ADMIN = 0;
    const STATUS_UPDATED_BY_USER = 1;
    const STATUS_CLOSED = 2;

    public static function tableName()
    {
        return '{{%tickets}}';
    }

    public function rules()
    {
        return [
            [['user_id', 'status','important'], 'integer'],
            [['subject', 'email', 'name', 'status'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['subject', 'email', 'name','comment'], 'string', 'max' => 255],
        ];
    }

    public function isClosed()
    {
        return $this->status == self::STATUS_CLOSED;
    }

    public function getMessages()
    {
        return $this->hasMany(TicketMessages::class, ['ticket_id' => 'id']);
    }

    public function getLastMessage()
    {
        return $this->getMessages()->orderBy('id DESC')->one();
    }

    public function getLastAnswer()
    {
        return $this->getMessages()->orderBy('id DESC')->andWhere(['is_admin' => 1])->one();
    }

    public function getUser()
    {
        $module = Yii::$app->getModule('helpdesk');
        return $this->hasOne($module->userClass, ['id' => 'user_id']);
    }

    public function attributeLabels()
    {
        return [
            'subject' => 'Тема обращения',
            'email' => 'Ваш e-mail',
            'name' => 'Ваше имя',
            'status' => 'Статус тикета',
        ];
    }
}
