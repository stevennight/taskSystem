<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "{{%system}}".
 *
 * @property int $id
 * @property string $name 系统名
 * @property string $remark 备注
 * @property int $created_by 创建人ID
 * @property int $created_at 创建时间
 * @property int $deleted_at 删除时间
 */
class System extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%system}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_by', 'created_at', 'deleted_at'], 'integer'],
            [['name'], 'string', 'max' => 140],
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
            'name' => '系统名',
            'remark' => '备注',
            'created_by' => '创建人ID',
            'created_at' => '创建时间',
            'deleted_at' => '删除时间',
        ];
    }
}
