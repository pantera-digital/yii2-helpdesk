<ul class="media-list">
    <?php foreach ($messages as $message):?>
        <?php $model = $message->ticket;?>
        <?php if(!$message->is_admin):?>
            <li class="media">
                <div class="media-body">
                    <h5 class="media-heading"><?=$model->user_id ? $model->user->profile->name : 'Аноним' ?> (<?=Yii::$app->formatter->asDatetime($message->created_at)?>)</h5>
                    <?=$message->message?>
                </div>
                <div class="media-right">
                    <a href="#">
                        <img style="height:50px;" class="media-object"  src="https://t4.ftcdn.net/jpg/01/12/09/17/500_F_112091769_vWEmDiwVIpO4H1plGuhYgnmduTuiGUh2.jpg" alt="...">
                    </a>
                </div>
            </li>
        <?php else:?>
            <li class="media media-admin">
                <div class="media-left">
                    <a href="#">
                        <img style="height:50px;" class="media-object" src="https://st2.depositphotos.com/8440746/11228/v/950/depositphotos_112286670-stock-illustration-laptop-user-icon-computer-device.jpg" alt="...">
                    </a>
                </div>
                <div class="media-body">
                    <h5 class="media-heading">Администратор (<?=Yii::$app->formatter->asDatetime($message->created_at)?>)</h5>
                    <?=$message->message?>
                    <?php if (!empty($message->mediaOther)):  ?>
                        <ul id="message-files">
                            <?php foreach ($message->mediaOther as $key => $file): ?>
                                <li>
                                    <?=\yii\helpers\Html::a($file->name, ['download', 'id' => $file->id]) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </li>
        <?php endif;?>
    <?php endforeach;?>
</ul>