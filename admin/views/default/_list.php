<?php

use pantera\helpdesk\models\Tickets;
use pantera\helpdesk\models\TicketMessages;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\helpers\Html;

$lastAnswer = $model->getLastAnswer();
$lastMessage = $model->getLastMessage();

/** @var Tickets $model */

?><style>
.media {
    padding:5px;
    border:1px solid #000000;
    margin-bottom: 0;
}
.media.media-admin {
    background: #f9f5ea;
}
</style>

<div class="panel panel-<?= ($model->status == Tickets::STATUS_CLOSED ? 'default' : ($model->status == Tickets::STATUS_UPDATED_BY_USER ? 'danger' : 'success')) ?>">
    <div class="panel-heading" style="cursor:pointer;" onclick="$(this).parent('.panel').find('.panel-body').toggle()">
        <div class="row">
            <div class="col-md-3">
                [#<?= str_pad($model->id, 4, 0, STR_PAD_LEFT) ?>] <b><?= $model->subject ?></b>
                <span class="<?= $model->important == 0 ? 'hidden' : '' ?> badge badge-success important-label">
                    важное
                </span>
            </div>
            <div class="col-md-3">
                <span>
                    <?= Yii::$app->formatter->asDatetime($model->last_message, 'long') ?>
                </span>
            </div>
            <div class="col-md-3">
                <?=$model->name?> (<?= Html::a($model->email, 'mailto:' . $model->email) ?>)
            </div>
            <div class="col-md-3">
                <?php if (!$model->user_id): ?>
                    Аноним
                <?php else: ?>
                    <a targe="_blank" href="<?= Url::to(['/user/update', ['id' => $model->user_id]]) ?>">
                        <?= $model->user->profile->name ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="panel-body" style="display:none;">
        <div class="row">
            <div class="col-md-6">
                <h5>Сообщения</h5>
                <div class="messages-wrapper">
                    <?= $this->render('_messages',[
                        'messages' => array_reverse($model->getMessages()->limit(2)->orderBy('id DESC')->all())
                    ]); ?>
                </div>
                <div class="controls">
                    <a href="<?= Url::to(['important','id' => $model->id]);?>" class="important">
                        <?= $model->important ? 'Убрать из важного' : 'Пометить как важное' ?>
                    </a>
                    <a href="#" onclick="$('#comment-form-<?=$model->id?>').toggleClass('hidden')">
                        Комментарий
                    </a>
                    <?php if ($model->status != Tickets::STATUS_CLOSED): ?>
                        <?= Html::a('В архив', ['close', 'id' => $model->id]); ?>
                    <?php endif; ?>
                    <?php if ($model->getMessages()->count() > 1): ?>
                        <a href="<?= Url::to(['all-messages', 'id' => $model->id]) ?>" class="all-messages">
                            Показать всю историю
                            (<?= $model->getMessages()->count() ?> сообщений)
                        </a>
                        <a href="<?= Url::to(['all-messages', 'id' => $model->id, 'limit' => 2]) ?>" class="hide-history hidden">
                            Скрыть историю
                        </a>
                    <?php endif; ?>
                </div>
                <?php if ($comment = $model->comment): ?>
                    <h5>Комментарий</h5>
                    <div class="alert alert-warning">
                        <?= $comment ?>
                    </div>
                <?php endif;?>
                <?php $commentForm = ActiveForm::begin([
                    'action' => ['comment', 'id' => $model->id],
                    'options' => [
                        'class' => 'hidden',
                        'id' => 'comment-form-' . $model->id,
                    ],
                ]); ?>
                    <?= $commentForm->field($model,'comment')->textInput(['placeholder' => 'Введите комментарий'])->label(false); ?>
                    <?= Html::submitButton('Сохранить комментарий',['class' => 'btn btn-success btn-block btn-sm']); ?>
                <?php ActiveForm::end(); ?>
            </div>
            <?php if ($model->status != $model::STATUS_CLOSED): ?>
                <div class="col-md-6">
                    <h5>Ответить</h5>
                    <div class="form">
                        <?php
                        $newMessage = new TicketMessages();
                        $newMessage->message = "Добрый день, " . trim($model->name). ", благодарим за ваше обращение! ";
                        $form = ActiveForm::begin([
                            'action' => ['create','id' => $model->id],
                            'id' => "response-form-for-ticket-{$model->id}",
                            'options' => ['class' => 'form form-horizontal ticket-response-form'],
                        ]); ?>
                        <?= $form->field($newMessage, 'message')
                            ->textarea(['placeholder' => $newMessage->getAttributeLabel('message'), 'rows' => 10])
                            ->label(false);
                        ?>
                        <?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
                            'model' => $newMessage,
                            'bucket' => 'mediaOther',
                            'urlUpload' => ['file-upload-innostudio', 'id' => $newMessage->id],
                            'urlDelete' => ['file-delete-innostudio'],
                            'pluginOptions' => [
                                'limit' => 10,
                            ],
                        ]) ?>
                        <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary btn-block']); ?>
                        <?php ActiveForm::end(); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
