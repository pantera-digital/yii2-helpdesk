<?php

use yii\helpers\Url;

?><h4>Тикеты</h4>

<?php if (!empty($tickets)): ?>
    <ul class="tickets">
        <?php foreach ($tickets as $ticket): ?>
            <li class="<?= (Yii::$app->request->get('id') === $ticket->id ? 'active' : '') ?><?= $ticket->isClosed() ? ' archive' : '' ?>">
                <a href="<?= Url::to(['/helpdesk/default/view','id' => $ticket->id]) ?>">
                    <div class="row">
                        <div class="col-md-4">
                            <?= Yii::$app->formatter->asDate($ticket->created_at, 'long')?><br />
                            <?= Yii::$app->formatter->asTime($ticket->created_at,'php:H:i') ?>
                        </div>
                        <div class="col-md-8">
                            <b><?= $ticket->subject ?></b><br />
                            <?= $ticket->getMessages()->orderBy('id DESC')->one()->message; ?>
                        </div>
                    </div>
                </a>
            </li>
        <?php endforeach ?>
    </ul>
<?php else: ?>
    <div class="alert alert-info">У вас нет открытых тикетов</div>
<?php endif; ?>
