<?php

namespace pantera\helpdesk\models;

use Yii;

/**
 * This is the model class for table "{{%ticket_message_file}}".
 *
 * @property int $id
 * @property int $message_id Message identity from ticket_message
 * @property string $file File path
 *
 * @property TicketMessages $ticketMessages
 */
class TicketMessageFile extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%ticket_message_file}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['message_id', 'file'], 'required'],
            [['message_id'], 'integer'],
            [['file'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'message_id' => 'Message identity from ticket_message',
            'file' => 'File path',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketMessage()
    {
        return $this->hasOne(TicketMessages::className(), ['id' => 'message_id']);
    }
}
