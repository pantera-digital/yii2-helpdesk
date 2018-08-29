<?php
\pantera\helpdesk\Assets::register($this)
?>
<div class="card">
    <div class="card-body card-padding">
        <div class="row">
            <?php if(!Yii::$app->user->can('admin')):?>
                <div class=<?=!Yii::$app->user->isGuest ? '"col-md-6"' : '"col-md-12"'?>>
                    <?= $this->render('_form', ['model' => $model, 'newMessage' => $newMessage]); ?>
                </div>
            <?php endif;?>
            <?php if(!Yii::$app->user->isGuest):?>
            <div class=<?=!Yii::$app->user->can('admin') ? '"col-md-6"' : '"col-md-12"'?>>
                <?php if(Yii::$app->user->can('admin')):?>
                    <?= $this->render('_tickets', [
                        'tickets' => $ticketsForAdmin,
                    ]); ?>
                <?php else:?>
                    <?= $this->render('_tickets', [
                            'tickets' => $tickets,
                    ]); ?>
                <?php endif;?>
            </div>
            <?php endif;?>
        </div>
    </div>
</div>
