<?php declare(strict_types=1);

/**
 * @var TaskStatisticsForm $searchModel
 * @var ActiveDataProvider $dataProvider
 */

use app\models\TaskStatisticsForm;
use yii\data\ActiveDataProvider;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

// 搜索栏
$form = ActiveForm::begin([
    'id' => 'task-index-form',
    'method' => 'GET'
]);
echo $form->field($searchModel, 'beginAt');
echo $form->field($searchModel, 'endAt');
?>
    <div class="form-group">
        <div class="col-lg-offset-1 col-lg-11">
            <?= Html::submitButton('提交', ['class' => 'btn btn-primary']) ?>
        </div>
    </div>
<?php
ActiveForm::end();

// 列表
echo Html::a('添加', ['create'], ['class' => 'btn btn-primary']);
echo Html::a('统计', ['statistics'], ['class' => 'btn btn-primary']);

echo GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        ['class' => 'yii\grid\SerialColumn'],
        'system.name',
        'module.name',
        'title',
        'score',
        'date',
        'begin_at',
        'end_at',
        [
            'class' => 'yii\grid\ActionColumn',
        ]
    ]
]);

$str = '';
foreach($dataProvider->getModels() as $key => $val) {
    $str = sprintf('%s - %s %s：%s-%s %s' . PHP_EOL, $val->begin_at, $val->end_at, $val->system->name, $val->module->name, $val->title, $val->desc) . $str;
}
echo Html::textarea('output', $str, [
    'style' => 'width: 100%; height: 120px;'
]);
?>
