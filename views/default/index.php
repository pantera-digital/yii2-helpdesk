<?php
\pantera\helpdesk\Assets::register($this)
?>
<div class="card">
    <div class="card-body card-padding">
        <div class="row">
            <div class=<?=!Yii::$app->user->isGuest ? '"col-md-6"' : '"col-md-12"'?>>
                <?= $this->render('_form', ['model' => $model, 'newMessage' => $newMessage]); ?>
            </div>
            <?php if(!Yii::$app->user->isGuest):?>
                <div class="col-md-6">
                    <?= $this->render('_tickets', [
                            'tickets' => $tickets,
                    ]); ?>
                </div>
            <?php endif;?>
        </div>
    </div>
</div>
