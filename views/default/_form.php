<?php

use yii\widgets\ActiveForm;
use yii\helpers\Html;

$readonly = (Yii::$app->user->isGuest ? [] : ['readonly' => 'readonly']);

?><h4 style="margin-left: 0;">Новый вопрос</h4>

<?php $form = ActiveForm::begin([
    'id' => 'tickets-form',
    'action' => ['/helpdesk/default/create'],
]); ?>

    <?= $form->field($model, 'subject')->textInput([
            'placeholder' => $model->getAttributeLabel('subject')
    ])->label(false) ?>

    <?= $form->field($model, 'email')->textInput(\yii\helpers\ArrayHelper::merge([
            'type' => 'email',
            'placeholder' => $model->getAttributeLabel('email')
    ], $readonly))->label(false) ?>

    <?= $form->field($model, 'name')->textInput(\yii\helpers\ArrayHelper::merge([
            'placeholder' => $model->getAttributeLabel('name')
    ], $readonly))->label(false) ?>

    <?= $form->field($newMessage, 'message')->textarea([
            'placeholder' => 'Ваше сообщение',
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
        <div class="g-recaptcha" data-sitekey="<?= $this->context->module->googleCaptchaSiteKey ?>"></div>
    <?php endif; ?>

    <?= Html::submitButton('Создать вопрос', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>
