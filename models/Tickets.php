<?php

namespace pantera\helpdesk\models;

use pantera\helpdesk\Module;
use Yii;

/**
 * This is the model class for table "{{%tickets}}".
 *
 * @property int $id
 * @property string $user_id User identity
 * @property string $subject Ticket theme
 * @property string $email User e-mail
 * @property string $name User name
 * @property int $status Ticket status
 * @property string $created_at
 * @property string $updated_at
 *
 * @property TicketMessages[] $messages
 */
class Tickets extends \yii\db\ActiveRecord
{
    const STATUS_UPDATED_BY_ADMIN = 0;
    const STATUS_UPDATED_BY_USER = 1;
    const STATUS_CLOSED = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tickets}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'status','important'], 'integer'],
            [['subject', 'email', 'name', 'status'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['subject', 'email', 'name','comment'], 'string', 'max' => 255],
        ];
    }

    public function getUser() {
        /** @var Module $hd */
        $hd = Yii::$app->getModule('helpdesk');
        return $this->hasOne($hd->userClass, ['id' => 'user_id']);
    }


    public function getLastAnswer()
    {
        return $this->getMessages()->orderBy('id DESC')->andWhere(['is_admin' => 1])->one();
    }

    public function getLastMessage() {
        return $this->getMessages()->orderBy('id DESC')->one();
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User identity',
            'subject' => 'Ticket theme',
            'email' => 'User e-mail',
            'name' => 'User name',
            'status' => 'Ticket status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMessages()
    {
        return $this->hasMany(TicketMessages::className(), ['ticket_id' => 'id']);
    }
}
