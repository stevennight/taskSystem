<?php declare(strict_types=1);

namespace app\controllers;

use app\models\Module;
use app\models\System;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;

/**
 * 模块管理
 *
 * Class ModuleController
 * @package app\controllers
 */
class ModuleController extends Controller
{
    /**
     * 模块管理 - 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = Module::find()->where(['deleted_at' => 0])
            ->with('system');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider
        ]);
    }

    /**
     * 模块管理 - 添加
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new Module();
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($model->load($params) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }

        $systemModel = System::find()->where([
            'deleted_at' => 0
        ])
            ->select(['id', 'name'])
            ->indexBy('id')
            ->all();
        $systemModel = array_map(function ($val) {
            return $val['name'];
        }, $systemModel);
        return $this->render('form', [
            'model' => $model,
            'systemModel' => $systemModel
        ]);
    }

    /**
     * 模块管理 - 编辑
     */
    public function actionUpdate()
    {
        $model = Module::findOne(['id' => Yii::$app->request->get('id', 0)]);
        if (!$model) {
            return $this->redirect('index');
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }


        $systemModel = System::find()->where([
            'deleted_at' => 0
        ])
            ->select(['id', 'name'])
            ->indexBy('id')
            ->all();
        $systemModel = array_map(function ($val) {
            return $val['name'];
        }, $systemModel);
        return $this->render('form', [
            'model' => $model,
            'systemModel' => $systemModel
        ]);
    }

    /**
     * 模块管理 - 删除
     *
     * @return Response
     */
    public function actionDelete()
    {
        $model = Module::findOne(['id' => Yii::$app->request->get('id', 0), 'deleted_at' => 0]);
        if (!$model) {
            return $this->redirect('index');
        }

        if (Yii::$app->request->isPost) {
            $model->deleted_at = time();
            $model->save();
        }
        return $this->redirect(['index']);
    }

    /**
     * 通过id获取模块
     */
    public function actionGet()
    {
        $id = Yii::$app->request->get('id');
        $model = [];
        if ($id) {
            $model = Module::findAll([
                'system_id' => $id,
                'deleted_at' => 0
            ]);
        }
        $return = '<option>请选择一个模块</option>';
        foreach ($model as $value) {
            $return .= '<option value="' . $value['id'] . '">' . $value['name'] . '</option>';
        }
        echo $return;
    }
}