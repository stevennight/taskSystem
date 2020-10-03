<?php declare(strict_types=1);

use app\models\TaskStatisticsForm;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var TaskStatisticsForm $model
 * @var array $systemStatisticsModel
 * @var array $moduleStatisticsModel
 * @var array $sumModel
 * @var array $systemSumModel
 */

$form = ActiveForm::begin([
    'id' => 'task-statistics-form',
    'method' => 'GET'
//    'options' => ['class' => 'form-horizontal'],
]);
echo $form->field($model, 'beginAt');
echo $form->field($model, 'endAt');
?>
<div class="form-group">
    <div class="col-lg-offset-1 col-lg-11">
        <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
    </div>
</div>
<?php
ActiveForm::end();
?>

<?php
// 系统分类
if ($systemStatisticsModel) {
    echo '<p>总绩效点：' . ($sumModel['score'] / 100) .'；总时长：' . $sumModel['use_time'] .'秒（' . ($sumModel['use_time'] / 60) . '分钟、' . $sumModel['use_time'] / 3600 . '小时）</p>';

    $dataProvider = new ArrayDataProvider();
    $dataProvider->setModels($systemStatisticsModel);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'system_name:text:系统',
            'score' => [
                'label' => '绩效点',
                'value' => function ($m) {
                    return $m['score'] / 100;
                }
            ],
            [
                'label' => '绩效点占比',
                'value' => function ($model) use ($sumModel) {
                    $res = $sumModel['score'] ? $model['score'] / $sumModel['score'] * 100 : 100;
                    return sprintf('%.2f%%', round($res, 2));
                }
            ],
            'use_time:text:时间',
            [
                'label' => '时间占比',
                'value' => function ($model) use ($sumModel) {
                    return sprintf('%.2f%%', round($model['use_time'] / $sumModel['use_time'] * 100, 2));
                }
            ]
        ]
    ]);
}


// 模块分类
if ($moduleStatisticsModel) {
    $dataProvider = new ArrayDataProvider();
    $dataProvider->setModels($moduleStatisticsModel);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'system_name:text:系统',
            'module_name:text:模块',
            'score' => [
                'label' => '绩效点',
                'value' => function ($m) {
                    return $m['score'] / 100;
                }
            ],
            [
                'label' => '系统绩效点占比',
                'value' => function ($model) use ($systemSumModel) {
                    $res = $systemSumModel[$model['system_id']]['score'] ? $model['score'] / $systemSumModel[$model['system_id']]['score'] * 100 : 100;
                    return sprintf('%.2f%%', round($res, 2));
                }
            ],[
                'label' => '总绩效点占比',
                'value' => function ($model) use ($sumModel) {
                    $res = $sumModel['score'] ? $model['score'] / $sumModel['score'] * 100 : 100;
                    return sprintf('%.2f%%', round($res, 2));
                }
            ],
            'use_time:text:时间',
            [
                'label' => '系统时间占比',
                'value' => function ($model) use ($systemSumModel) {
                    return sprintf('%.2f%%', round($model['use_time'] / $systemSumModel[$model['system_id']]['use_time'] * 100, 2));
                }
            ],
            [
                'label' => '总时间占比',
                'value' => function ($model) use ($sumModel) {
                    return sprintf('%.2f%%', round($model['use_time'] / $sumModel['use_time'] * 100, 2));
                }
            ]
        ]
    ]);
}
?>
