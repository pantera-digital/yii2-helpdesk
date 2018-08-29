<div class="card">
    <div class="card-header">
        <h4>Отправить сообщение</h4>
    </div>
    <div class="card-body card-padding">
        <?php if (Yii::$app->user->can('admin')) {
            $newMessage->message = "Добрый день, {$ticket->name}, спасибо за ваше обращение!\n";
        }
        $form = \yii\widgets\ActiveForm::begin([
            'id' => 'tickets-form',
            'action' => ['create','id' => $ticket->id],
            'options' => [
                'class' => 'form form-horizontal',
            ],
        ]); ?>
        <?=$form->field($newMessage,'message')->textarea(['placeholder' => $newMessage->getAttributeLabel('message'), 'rows' => 10])->label(false) ?>
        <?=$form->field($ticket,'important')->checkbox() ?>
        <?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
            'model' => $newMessage,
            'bucket' => 'mediaOther',
            'urlUpload' => ['file-upload-innostudio', 'id' => $newMessage->id],
            'urlDelete' => ['file-delete-innostudio'],
            'pluginOptions' => [
                'limit' => 10,
            ],
        ]) ?>
        <?= \yii\helpers\Html::submitButton('Отправить', ['class' => 'btn btn-primary']) ?>
        <?= \yii\helpers\Html::a('Закрыть тему', ['close', 'id' => $ticket->id]) ?>
        <?php \yii\widgets\ActiveForm::end() ?>

    </div>
</div>
