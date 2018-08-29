<?php

namespace pantera\helpdesk\models;

use Yii;

/**
 * This is the model class for table "{{%ticket_messages}}".
 *
 * @property int $id
 * @property int $ticket_id Ticket ID from tickets table
 * @property int $is_admin Admin or user message
 * @property string $message Message text
 * @property string $created_at
 *
 * @property TicketMessageFile $id0
 * @property Tickets $tickets
 */
class TicketMessages extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class' => \pantera\media\behaviors\MediaUploadBehavior::className(),
                'buckets' => [
                    'mediaOther' => [
                        'multiple' => true,
                    ],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
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
            [['ticket_id', 'is_admin'], 'integer'],
            [['message'], 'string'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ticket_id' => 'Ticket ID from tickets table',
            'is_admin' => 'Admin or user message',
            'message' => 'Message text',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Tickets::className(), ['id' => 'ticket_id']);
    }
}
