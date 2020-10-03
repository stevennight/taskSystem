<?php declare(strict_types=1);

/**
 * @var Task $model
 */

use app\models\Task;
use yii\helpers\Html;
use yii\widgets\DetailView;

echo Html::a('返回', null, [
    'class' => 'btn btn-primary',
    'onclick' => 'history.back()'
]);

echo DetailView::widget([
    'model' => $model,
    'attributes' => [
        'id',
        'title',
        'system.name',
        'module.name',
        [
            'format' => 'html',
            'attribute' => 'desc',
            'value' => function ($m) {
                return nl2br($m['desc']);
            }
        ],
        'score',
        'date',
        'begin_at',
        'end_at',
        [
            'attribute' => 'use_time',
            'value' => function ($model) {
                $second = $model->use_time;
                $min = round($second / 60, 2);
                $hour = round($min / 60, 2);
                return sprintf('%.2f秒（%.2f分钟，%.2f小时）', $second, $min, $hour);
            },
        ],
    ],
]);