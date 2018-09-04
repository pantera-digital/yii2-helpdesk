<?php
use Carbon\Carbon;

/**
 * @var \pantera\helpdesk\models\Tickets $model
 */
$lastAnswer = $model->getLastAnswer();
$lastMessage = $model->getLastMessage();
?>
<div class="panel panel-<?= ($model->status == \pantera\helpdesk\models\Tickets::STATUS_CLOSED ? 'default' : ($model->status == \pantera\helpdesk\models\Tickets::STATUS_UPDATED_BY_USER ? 'danger' : 'success')) ?>">
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
                <?=$model->name?> (<?=\yii\helpers\Html::a($model->email,'mailto:'.$model->email)?>)
            </div>
            <div class="col-md-3">
                <?php if (!$model->user_id): ?>
                    Аноним
                <?php else: ?>
                    <a targe="_blank" href="<?=\yii\helpers\Url::to(['/user/update',['id' => $model->user_id]])?>">
                        <?= $model->user->profile->name ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <style>
        .media {
            padding:5px;
            border:1px solid #000000;
            margin-bottom: 0;
        }
        .media.media-admin {
            background: #f9f5ea;
        }
    </style>
    <div class="panel-body" style="display:none;">
        <div class="row">
            <div class="col-md-6">
                <h5>Сообщения</h5>
                <div class="messages-wrapper">
                    <?= $this->render('_messages',[
                        'messages' => array_reverse($model->getMessages()->limit(2)->orderBy('id DESC')->all())
                    ]) ?>
                </div>
                <div class="controls">
                    <a href="<?=\yii\helpers\Url::to(['important','id' => $model->id]);?>" class="important <?= $model->important == 1 ? 'hidden' : '' ?>">Пометить
                        как важное
                    </a>
                    <a href="<?=\yii\helpers\Url::to(['important','id' => $model->id]);?>" class="important <?= $model->important == 0 ? 'hidden' : '' ?>">Убрать из
                        важного
                    </a>
                    <a href="#" onclick="$('#comment-form-<?=$model->id?>').toggleClass('hidden')">Комментарий</a>
                    <?php if ($model->status != \pantera\helpdesk\models\Tickets::STATUS_CLOSED): ?>
                        <?=\yii\helpers\Html::a('В архив',['close', 'id' => $model->id]) ?>
                    <?php endif; ?>
                    <?php if ($model->getMessages()->count() > 1): ?>
                        <a href="<?=\yii\helpers\Url::to(['all-messages','id' => $model->id])?>" class="all-messages">
                            Показать всю историю
                            (<?= $model->getMessages()->count() ?> сообщений)
                        </a>
                        <a href="<?=\yii\helpers\Url::to(['all-messages','id' => $model->id, 'limit' => 2])?>" class="hide-history hidden">Скрыть историю</a>
                    <?php endif; ?>
                </div>
                    <?php if($model->comment):?>
                        <h5>Комментарий</h5>
                        <div class="alert alert-warning">
                            <?=$model->comment?>
                        </div>
                    <?php endif;?>
                    <?php $commentForm = \yii\widgets\ActiveForm::begin([
                            'action' => ['comment', 'id' => $model->id],
                            'options' => [
                                    'class' => 'hidden',
                                    'id' => 'comment-form-'.$model->id
                            ]
                    ]) ?>
                    <?=$commentForm->field($model,'comment')->textInput(['placeholder' => 'Введите комментарий'])->label(false) ?>
                    <?= \yii\helpers\Html::submitButton('Сохранить комментарий',['class' => 'btn btn-success btn-block btn-sm']) ?>
                <?php \yii\widgets\ActiveForm::end();?>
            </div>
            <?php if($model->status != $model::STATUS_CLOSED):?>
                <div class="col-md-6">
                    <h5>Ответить</h5>
                    <div class="form">
                        <?php
                        $newMessage = new \pantera\helpdesk\models\TicketMessages();
                        $ticket = $model;
                        ?>
                        <?php if (Yii::$app->user->can('admin')) {
                            $newMessage->message = "Добрый день, {$ticket->name}, спасибо за ваше обращение!\n";
                        }
                        $form = \yii\widgets\ActiveForm::begin([
                            'action' => ['create','id' => $ticket->id],
                            'id' => 'response-form-for-ticket-' . $ticket->id,
                            'options' => [
                                'class' => 'form form-horizontal ticket-response-form',
                            ],
                        ]); ?>
                        <?=$form->field($newMessage,'message')->textarea(['placeholder' => $newMessage->getAttributeLabel('message'), 'rows' => 10])->label(false) ?>
                        <?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
                            'model' => $newMessage,
                            'bucket' => 'mediaOther',
                            'urlUpload' => ['file-upload-innostudio', 'id' => $newMessage->id],
                            'urlDelete' => ['file-delete-innostudio'],
                            'pluginOptions' => [
                                'limit' => 10,
                            ],
                        ]) ?>
                        <?= \yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-primary btn-block']) ?>
                        <?php \yii\widgets\ActiveForm::end() ?>
                    </div>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
