<?php
$this->title = 'Вопросы';
\pantera\helpdesk\Assets::register($this);
?>

<?php if (!Yii::$app->user->can('admin')): ?>
        <?= \yii\helpers\Html::a('Новый вопрос', ['index'], ['class' => 'btn btn-default']) ?>
<?php endif; ?>
<div class="row">
    <div class="col-md-6">
        <?php if ($ticket->status !== \pantera\helpdesk\models\Tickets::STATUS_CLOSED): ?>
            <?= $this->render('_formRequest', array('newMessage' => $newMessage, 'ticket' => $ticket)); ?>
        <?php else: ?>
            <div class="alert alert-info" style="margin-top: <?= (UserModule::isAdmin() ? '20px' : '') ?>;">Тикет закрыт</div>
        <?php endif; ?>
        <?= $this->render('_messages', array('messages' => $messages)); ?>
    </div>
    <?php if (!is_null($userTickets)): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body card-padding">
                    <?= $this->render('_tickets', array('tickets' => $ticketsForAdmin ?: $userTickets)); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>