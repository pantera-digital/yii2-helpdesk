<?php

namespace pantera\helpdesk\models;

use Yii;

/**
 * @property int $id
 * @property int $ticket_id Ticket ID from tickets table
 * @property int $is_admin Admin or user message
 * @property string $message Message text
 * @property string $created_at
 * @property TicketMessageFile $id0
 * @property Tickets $tickets
 */
class TicketMessages extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => \pantera\media\behaviors\MediaUploadBehavior::class,
                'buckets' => [
                    'mediaOther' => [
                        'multiple' => true,
                    ],
                ],
            ],
        ];
    }

    public static function tableName()
    {
        return '{{%ticket_messages}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ticket_id', 'message'], 'required'],
            [['ticket_id'], 'integer'],
            [['is_admin'], 'boolean'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    public function getTicket()
    {
        return $this->hasOne(Tickets::class, ['id' => 'ticket_id']);
    }

    public function attributeLabels()
    {
        return [
            'message' => 'Сообщение',
        ];
    }
}
