<?php
/**
 * @var $searchModel \pantera\helpdesk\models\TicketsSearch
 * @var $this \yii\web\View
 */
\pantera\helpdesk\Assets::register($this);
$this->params['breadcrumbs'][] = 'Helpdesk';
?>
<ul class="nav nav-tabs material-tab">
    <li class="<?= $searchModel->withoutResponse ? 'active' : '' ?>">
        <a href="<?=\yii\helpers\Url::to(['index',
                'TicketsSearch' => [
                    'withoutResponse' => 1
                ]
        ])?>">
            Без ответа
            <span class="label label-danger">
                    <?= \pantera\helpdesk\models\Tickets::find()->andWhere(['status' => \pantera\helpdesk\models\Tickets::STATUS_UPDATED_BY_USER])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->responsed ? 'active' : '' ?>">
        <a href="<?=\yii\helpers\Url::to(['index',
            'TicketsSearch' => [
                'responsed' => 1
            ]
        ])?>">
            Отвеченные
            <span class="label label-success">
                    <?= \pantera\helpdesk\models\Tickets::find()->andWhere(['status' => \pantera\helpdesk\models\Tickets::STATUS_UPDATED_BY_ADMIN])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->important ? 'active' : '' ?>">
        <a href="<?=\yii\helpers\Url::to(['index',
            'TicketsSearch' => [
                'important' => 1
            ]
        ])?>">
            Важные
            <span class="label label-warning">
                    <?= \pantera\helpdesk\models\Tickets::find()->andWhere(['important' => 1])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->withComments ? 'active' : '' ?>">
        <a href="<?=\yii\helpers\Url::to(['index',
            'TicketsSearch' => [
                'withComments' => 1
            ]
        ])?>">
            С комментарием
            <span class="label label-default">
                    <?= \pantera\helpdesk\models\Tickets::find()->andWhere(['IS NOT', 'comment', null])->count() ?>
            </span>
        </a>
    </li>
    <li class="<?= $searchModel->archive ? 'active' : '' ?>">
        <a href="<?=\yii\helpers\Url::to(['index',
            'TicketsSearch' => [
                'archive' => 1
            ]
        ])?>">
            Архив
            <span class="label label-default">
                    <?= \pantera\helpdesk\models\Tickets::find()->andWhere(['status' => \pantera\helpdesk\models\Tickets::STATUS_CLOSED])->count() ?>
            </span>
        </a>
    </li>
</ul>
<br>
<?=\yii\widgets\ListView::widget([
        'id' => 'tickets-list',
        'summary' => false,
        'dataProvider' => $dataProvider,
        'itemView' => '_list',
]);?>