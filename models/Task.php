<?php

namespace app\models;

use Yii;
use yii\validators\DateValidator;

/**
 * This is the model class for table "{{%task}}".
 *
 * @property int $id
 * @property int $system_id 系统id
 * @property int $module_id 模块id
 * @property string $title 简述
 * @property string $desc 详情
 * @property int $score 绩效点
 * @property string $date 日期
 * @property string $begin_at 开始时间
 * @property string $end_at 结束时间
 * @property int $use_time 总时长
 * @property int $created_by 创建人
 * @property int $created_at 创建时间
 * @property int $deleted_at 删除时间
 */
class Task extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%task}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['system_id', 'module_id', 'use_time', 'created_by', 'created_at', 'deleted_at'], 'integer'],
            [['score'], 'number'],
            [['system_id', 'module_id', 'date', 'begin_at', 'end_at', 'title'], 'required'],
            [['date', 'begin_at', 'end_at'], 'safe'],
            [['title'], 'string', 'max' => 50],
            [['desc'], 'string', 'max' => 255],
            [['date'], 'date', 'format' => 'php:Y-m-d'],
            [['begin_at', 'end_at'], 'date', 'format' => 'php:Hi'],
            ['score', 'default', 'value' => 0],
            ['module_id', 'moduleValidator']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'system_id' => '系统id',
            'module_id' => '模块id',
            'title' => '简述',
            'desc' => '详情',
            'score' => '绩效点',
            'date' => '日期',
            'begin_at' => '开始时间',
            'end_at' => '结束时间',
            'use_time' => '总时长',
            'created_by' => '创建人',
            'created_at' => '创建时间',
            'deleted_at' => '删除时间',
        ];
    }

    public function getSystem()
    {
        return $this->hasOne(System::class, ['id' => 'system_id']);
    }

    public function getModule()
    {
        return $this->hasOne(Module::class, ['id' => 'module_id']);
    }

    public function beforeSave($insert)
    {
        $this->begin_at = $this->date . ' ' . substr($this->begin_at, 0, 2) . ':' . substr($this->begin_at, 2, 2);
        $this->end_at = $this->date . ' ' . substr($this->end_at, 0, 2) . ':' . substr($this->end_at, 2, 2);

        $begin = strtotime($this->begin_at);
        $end = strtotime($this->end_at);
        $this->use_time = $end - $begin;
        $this->score = $this->score * 100;

        return parent::beforeSave($insert);
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->begin_at = date('H:i', strtotime($this->begin_at));
        $this->end_at = date('H:i', strtotime($this->end_at));
        $this->score = $this->score / 100;
    }

    /**
     * 检查模块的系统与选择的系统是否对应。
     * @param $attr
     */
    public function moduleValidator($attr)
    {
        $moduleModel = $this->module;
        if ((int)$moduleModel->system_id !== (int)$this->system_id) {
            $this->addError($attr, '模块和系统不对应');
        }
    }
}
