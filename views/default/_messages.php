<?php

use yii\helpers\Html;

?><?php foreach ($messages as $message): ?>
    <div class="card" id="messages">
        <div class="card-header">
            <h5>
                <?php if ($message->is_admin): ?>
                    Администрация
                <?php else: ?>
                    <?= $message->ticket->name; ?>
                <?php endif; ?>
                <div class="pull-right">
                    <?= Yii::$app->formatter->asDatetime($message->created_at) ?>
                    <?php if (Yii::$app->getModule('helpdesk')->isAdmin()): ?>
                        (<?= Html::a($message->ticket->email, 'mailto:' . $message->ticket->email) ?>)
                    <?php endif; ?>
                </div>
            </h5>
        </div>
        <div class="card-body ticket-message">
            <?= $message->message ?>
        </div>
        <div class="card-body">
            <?php if (!empty($message->mediaOther)): ?>
                <ul id="message-files">
                    <li>
                        Файлы:
                    </li>
                    <?php foreach ($message->mediaOther as $key => $file): ?>
                        <li>
                            <?= Html::a($file->name, ['download', 'id' => $file->id]); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
<?php endforeach; ?>
