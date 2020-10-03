<?php declare(strict_types=1);

namespace app\models;

use yii\base\Model;

class TaskStatisticsForm extends Model
{
    public $beginAt;

    public $endAt;

    public function rules()
    {
        return [
//            [['beginAt', 'endAt'], 'default', 'value' => date('Y-m-d')],
            [['beginAt', 'endAt'], 'date', 'format' => 'php:Y-m-d']
        ];
    }

    public function attributeLabels()
    {
        return [
            'beginAt' => '开始日期',
            'endAt' => '结束日期'
        ];
    }
}