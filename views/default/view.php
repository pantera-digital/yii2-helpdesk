<?php

use yii\helpers\Html;

$this->title = 'Вопросы';
$this->params['breadcrumbs'][] = ['url' => ['index'], 'label' => 'Helpdesk'];
$this->params['breadcrumbs'][] = 'Тикет [#' . str_pad($ticket->id, 4, 0, STR_PAD_LEFT) . ']';

\pantera\helpdesk\Assets::register($this);

?><div class="row">
    <div class="col-md-6">
        <?php if (!$ticket->isClosed()): ?>
            <?= $this->render('_formRequest', array('newMessage' => $newMessage, 'ticket' => $ticket)); ?>
        <?php else: ?>
            <div class="alert alert-info">Тикет закрыт</div>
        <?php endif; ?>
        <?= $this->render('_messages', array('messages' => $messages)); ?>
    </div>
    <?php if (!is_null($userTickets)): ?>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body card-padding">
                    <?= $this->render('_tickets', array('tickets' => $userTickets)); ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
