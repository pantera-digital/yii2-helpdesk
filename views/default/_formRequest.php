<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

if (Yii::$app->getModule('helpdesk')->isAdmin()) {
    $newMessage->message = "Добрый день, {$ticket->name}, спасибо за ваше обращение!\n";
}

?><div class="card">
    <div class="card-header">
        <h4>Отправить сообщение</h4>
    </div>
    <div class="card-body card-padding">
        <?php $form = ActiveForm::begin([
            'id' => 'tickets-form',
            'action' => ['create','id' => $ticket->id],
            'options' => [
                'class' => 'form form-horizontal',
            ],
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
            <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Закрыть тему', ['close', 'id' => $ticket->id]) ?>
        <?php ActiveForm::end() ?>
    </div>
</div>
