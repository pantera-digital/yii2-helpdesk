<?php

use pantera\helpdesk\models\Tickets;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = 'Helpdesk';

\pantera\helpdesk\Assets::register($this);

?><ul class="nav nav-tabs material-tab">
    <li class="<?= $searchModel->withoutResponse ? 'active' : '' ?>">
        <a href="<?= Url::to(['index', 'TicketsSearch' => ['withoutResponse' => 1]])?>">
            Без ответа
            <span class="label label-danger">
                <?= Tickets::find()->andWhere(['status' => Tickets::STATUS_UPDATED_BY_USER])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->responsed ? 'active' : '' ?>">
        <a href="<?= Url::to(['index', 'TicketsSearch' => ['responsed' => 1]]) ?>">
            Отвеченные
            <span class="label label-success">
                <?= Tickets::find()->andWhere(['status' => Tickets::STATUS_UPDATED_BY_ADMIN])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->important ? 'active' : '' ?>">
        <a href="<?= Url::to(['index', 'TicketsSearch' => ['important' => 1]]) ?>">
            Важные
            <span class="label label-warning">
                <?= Tickets::find()->andWhere(['important' => 1])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->withComments ? 'active' : '' ?>">
        <a href="<?= Url::to(['index', 'TicketsSearch' => ['withComments' => 1]]) ?>">
            С комментарием
            <span class="label label-default">
                <?= Tickets::find()->andWhere(['AND', ['!=', 'comment', ''], ['IS NOT', 'comment', null]])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->archive ? 'active' : '' ?>">
        <a href="<?= Url::to(['index', 'TicketsSearch' => ['archive' => 1]]) ?>">
            Архив
            <span class="label label-default">
                <?= Tickets::find()->andWhere(['status' => Tickets::STATUS_CLOSED])->count() ?>
            </span>
        </a>
    </li>
</ul>
<br>

<?= \yii\widgets\ListView::widget([
        'id' => 'tickets-list',
        'summary' => false,
        'dataProvider' => $dataProvider,
        'itemView' => '_list',
]); ?>
