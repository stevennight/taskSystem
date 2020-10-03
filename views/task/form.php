<?php declare(strict_types=1);

/**
 * @var System $model
 * @var array $systemModel
 * @var array $moduleModel
 */

use app\models\System;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JqueryAsset;
use yii\widgets\ActiveForm;

$form = ActiveForm::begin([
    'id' => 'task-form',
    'options' => ['class' => 'form-horizontal'],
])
?>
<?= $form->field($model, 'system_id')->label('系统')->dropdownList($systemModel,
    [
        'prompt' => '请选择对应的系统',
        'onchange' => '
            $.post("' . Url::to('/module/get') . '?id=" + $(this).val(), function(data){
                $("#task-module_id").html(data);
            });
        '
    ]
) ?>
<?= $form->field($model, 'module_id')->label('模块')->dropdownList($moduleModel,
    ['prompt' => '请选择对应的模块']
) ?>
<?= $form->field($model, 'title') ?>
<?= $form->field($model, 'desc')->textarea() ?>
<?= $form->field($model, 'score') ?>
<?= $form->field($model, 'date') ?>
<?= $form->field($model, 'begin_at') ?>
<?= $form->field($model, 'end_at') ?>

<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php ActiveForm::end() ?>
