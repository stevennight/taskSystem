<?php declare(strict_types=1);

namespace app\controllers;

use app\models\Module;
use app\models\System;
use app\models\Task;
use app\models\TaskStatisticsForm;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;
use function Webmozart\Assert\Tests\StaticAnalysis\null;

/**
 * 任务管理
 *
 * Class TaskController
 * @package app\controllers
 */
class TaskController extends Controller
{
    /**
     * 任务管理 - 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new TaskStatisticsForm();
        $searchModel->load(Yii::$app->request->get());
        $query = Task::find()->where(['deleted_at' => 0])
            ->andFilterWhere(['>=', 'begin_at', $searchModel->beginAt])
            ->andFilterWhere(['<=', 'begin_at', $searchModel->endAt ? $searchModel->endAt . ' 23:59:59' : null])
            ->with('system')
            ->with('module');
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 20,
            ],
            'sort' => [
                'defaultOrder' => [
                    'begin_at' => SORT_DESC
                ]
            ]
        ]);
        return $this->render('index', [
            'dataProvider' => $dataProvider,
            'searchModel' => $searchModel
        ]);
    }

    /**
     * 任务管理 - 添加
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new Task();
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($model->load($params) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }

        $systemModel = $this->getSystemModel();
        $moduleModel = [];

        // 设置默认值
        $model->date = strtotime('Y-m-d');

        return $this->render('form', [
            'model' => $model,
            'systemModel' => $systemModel,
            'moduleModel' => $moduleModel
        ]);
    }

    /**
     * 任务管理 - 编辑
     */
    public function actionUpdate()
    {
        $model = Task::findOne(['id' => Yii::$app->request->get('id', 0)]);
        if (!$model) {
            return $this->redirect('index');
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }

        $systemModel = $this->getSystemModel();
        $moduleModel = $this->getModuleModel($model ? $model->system_id : null);

        return $this->render('form', [
            'model' => $model,
            'systemModel' => $systemModel,
            'moduleModel' => $moduleModel
        ]);
    }

    /**
     * 任务管理 - 删除
     *
     * @return Response
     */
    public function actionDelete()
    {
        $model = Task::findOne(['id' => Yii::$app->request->get('id', 0), 'deleted_at' => 0]);
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
     * 任务管理 - 详情
     *
     * @return string|Response
     */
    public function actionView()
    {
        $id = Yii::$app->request->get('id');
        if (!$id) {
            return $this->redirect('index');
        }

        $model = Task::findOne(['id' => $id]);
        return $this->render('view', [
            'model' => $model
        ]);
    }

    /**
     * 任务管理 - 统计
     *
     * @return string
     */
    public function actionStatistics()
    {
        $model = new TaskStatisticsForm();
        $model->beginAt = $model->endAt = date('Y-m-d');
        if (
            ($model->load(Yii::$app->request->get()) && $model->validate()) ||
            $model->validate()
        ) {
            $sql = Task::find()
                ->alias('t')
                ->andFilterWhere(['>=', 't.begin_at', $model->beginAt])
                ->andFilterWhere(['<=', 't.begin_at', $model->endAt ? $model->endAt . ' 23:59:59' : null]);
            $systemQuery = clone $sql;
            $moduleQuery = clone $sql;
            $systemSumQuery = clone $sql;

            $sumModel = $sql->select(['SUM(t.score) AS score', 'SUM(t.use_time) AS use_time'])
                ->asArray()
                ->one();
            $systemStatisticsModel = $systemQuery->select(['s.name AS system_name', 'SUM(t.score) AS score', 'SUM(t.use_time) AS use_time'])
                ->groupBy(['t.system_id'])
                ->leftJoin(System::tableName() . 'AS s', 't.system_id = s.id')
                ->leftJoin(Module::tableName() . 'AS m', 't.module_id = m.id')
                ->orderBy('t.system_id')
                ->asArray()
                ->all();
            $systemSumModel = $systemSumQuery->select(['t.system_id', 'SUM(t.score) AS score', 'SUM(t.use_time) AS use_time'])
                ->groupBy(['t.system_id'])
                ->indexBy('system_id')
                ->asArray()
                ->all();
            $moduleStatisticsModel = $moduleQuery->select(['s.id AS system_id', 's.name AS system_name', 'm.name AS module_name', 'SUM(t.score) AS score', 'SUM(t.use_time) AS use_time'])
                ->groupBy(['t.system_id', 't.module_id'])
                ->leftJoin(System::tableName() . 'AS s', 't.system_id = s.id')
                ->leftJoin(Module::tableName() . 'AS m', 't.module_id = m.id')
                ->orderBy('t.system_id')
                ->asArray()
                ->all();
        }

        return $this->render('statistics', [
            'model' => $model,
            'systemStatisticsModel' => $systemStatisticsModel ?? [],
            'moduleStatisticsModel' => $moduleStatisticsModel ?? [],
            'sumModel' => $sumModel ?? [],
            'systemSumModel' => $systemSumModel ?? []
        ]);
    }

    /**
     * 获取系统
     * @return array
     */
    public function getSystemModel()
    {
        $systemModel = System::find()->where([
            'deleted_at' => 0
        ])
            ->select(['id', 'name'])
            ->indexBy('id')
            ->all();
        $systemModel = array_map(function ($val) {
            return $val['name'];
        }, $systemModel);
        return $systemModel;
    }

    /**
     * 获取模块
     * @param null $id 系统ID
     * @return string[]
     */
    public function getModuleModel($id = null)
    {
        $moduleModel = Module::find()->where([
            'm.deleted_at' => 0
        ])
            ->alias('m')
            ->filterWhere(['system_id' => $id])
            ->select(['m.id', 'm.name', 'm.system_id'])
            ->joinWith('system')
            ->indexBy('id')
            ->asArray()
            ->all();
        $moduleModel = array_map(function ($val) {
            return $val['name'];
        }, $moduleModel);
        return $moduleModel;
    }
}