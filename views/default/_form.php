    <h4 style="margin-left: 0;">Новый вопрос</h4>
    <?php
        $form = \yii\widgets\ActiveForm::begin([
            'id' => 'tickets-form',
            'action' => ['/helpdesk/default/create'],
        ]);
    ?>

    <?php $readonly = (Yii::$app->user->isGuest ? [] : ['readonly' => 'readonly']); ?>
    <?=$form->field($model,'subject')->textInput([
            'placeholder' => $model->getAttributeLabel('subject')
    ])->label(false) ?>
    <?=$form->field($model,'email')->textInput(\yii\helpers\ArrayHelper::merge([
            'type' => 'email',
            'placeholder' => $model->getAttributeLabel('email')
    ], $readonly))->label(false) ?>
    <?=$form->field($model,'name')->textInput(\yii\helpers\ArrayHelper::merge([
            'placeholder' => $model->getAttributeLabel('name')
    ], $readonly))->label(false) ?>
    <?=$form->field($newMessage,'message')->textarea([
            'placeholder' => $model->getAttributeLabel('message'),
            'rows' => 10
    ])->label(false) ?>
    <?= pantera\media\widgets\innostudio\MediaUploadWidgetInnostudio::widget([
        'model' => $newMessage,
        'bucket' => 'mediaOther',
        'urlUpload' => ['file-upload-innostudio', 'id' => $newMessage->id],
        'urlDelete' => ['file-delete-innostudio'],
        'pluginOptions' => [
            'limit' => 10,
        ],
    ]) ?>
    <?php if (Yii::$app->user->isGuest): ?>
                <div class="g-recaptcha" data-sitekey="<?=$this->context->module->googleCaptchaSiteKey?>"></div>
    <?php endif; ?>
        <?=\yii\helpers\Html::submitButton('Создать вопрос', ['class' => 'btn btn-primary']) ?>
    <?php \yii\widgets\ActiveForm::end() ?>
