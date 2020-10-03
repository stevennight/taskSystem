<?php declare(strict_types=1);
/**
 * @var System $model
 */

use app\models\System;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'login-form',
    'options' => ['class' => 'form-horizontal'],
])
?>
<?= $form->field($model, 'name') ?>
<?= $form->field($model, 'remark')->textarea() ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
