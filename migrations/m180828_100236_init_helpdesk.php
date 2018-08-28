<?php

use yii\db\Migration;

/**
 * Class m180828_100236_init_helpdesk
 */
class m180828_100236_init_helpdesk extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%tickets}}',[
            'id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->null()->comment('User identity'),
            'subject' => $this->string()->notNull()->comment('Ticket theme'),
            'email' => $this->string()->notNull()->comment('User e-mail'),
            'name' => $this->string()->notNull()->comment('User name'),
            'status' => $this->tinyInteger()->unsigned()->notNull()->comment('Ticket status'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
            'updated_at' => $this->timestamp()->null()->defaultExpression("CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP")
        ]);

        $this->createTable('{{%ticket_messages}}',[
            'id' => $this->primaryKey(),
            'ticket_id' => $this->integer()->notNull()->comment('Ticket ID from tickets table'),
            'is_admin' => $this->tinyInteger()->unsigned()->comment('Admin or user message'),
            'message' => $this->text()->notNull()->comment('Message text'),
            'created_at' => $this->timestamp()->notNull()->defaultExpression("CURRENT_TIMESTAMP"),
        ]);

        $this->createTable('{{%ticket_message_file}}',[
            'id' => $this->primaryKey(),
            'message_id' => $this->integer()->notNull()->comment('Message identity from ticket_message'),
            'file' => $this->string()->notNull()->comment('File path')
        ]);

        $this->createIndex('hlpdsk-tkt-msg-ix', '{{%ticket_messages}}', 'ticket_id');
        $this->addForeignKey('hpdsk-tkt-msg-fk','{{%tickets}}','id', '{{%ticket_messages}}', 'ticket_id');

        $this->createIndex('hlpdsk-tkt-msg-file-ix', '{{%ticket_message_file}}', 'message_id');
        $this->addForeignKey('hpdsk-tkt-msg-file-fk','{{%ticket_messages}}','id', '{{%ticket_message_file}}', 'message_id');


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%tickets}}');
        $this->dropTable('{{%ticket_messages}}');
        $this->dropTable('{{%ticket_message_file}}');
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180828_100236_init_helpdesk cannot be reverted.\n";

        return false;
    }
    */
}
