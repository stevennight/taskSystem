<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%module}}".
 *
 * @property int $id
 * @property int $system_id 系统id
 * @property string $name 模块名
 * @property string $remark 备注
 * @property int $created_by 创建人ID
 * @property int $created_at 创建时间
 * @property int $deleted_at 删除时间
 */
class Module extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%module}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['system_id', 'created_by', 'created_at', 'deleted_at'], 'integer'],
            [['name'], 'string', 'max' => 50],
            [['remark'], 'string', 'max' => 255],
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
            'name' => '模块名',
            'remark' => '备注',
            'created_by' => '创建人ID',
            'created_at' => '创建时间',
            'deleted_at' => '删除时间',
        ];
    }

    public function getSystem() {
        return $this->hasOne(System::class, ['id' => 'system_id']);
    }
}
