<?php declare(strict_types=1);

namespace app\controllers;

use app\models\System;
use Yii;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\Response;

/**
 * 系统管理
 *
 * Class SystemController
 * @package app\controllers
 */
class SystemController extends Controller
{
    /**
     * 系统管理 - 列表
     *
     * @return string
     */
    public function actionIndex()
    {
        $query = System::find()->where(['deleted_at' => 0]);
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
     * 系统管理 - 添加
     *
     * @return string
     */
    public function actionCreate()
    {
        $model = new System();
        if (Yii::$app->request->isPost) {
            $params = Yii::$app->request->post();
            if ($model->load($params) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    /**
     * 系统管理 - 编辑
     */
    public function actionUpdate()
    {
        $model = System::findOne(['id' => Yii::$app->request->get('id', 0)]);
        if (!$model) {
            return $this->redirect('index');
        }

        if (Yii::$app->request->isPost) {
            if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->save()) {
                return $this->redirect(['index']);
            }
        }

        return $this->render('form', [
            'model' => $model
        ]);
    }

    /**
     * 系统管理 - 删除
     *
     * @return Response
     */
    public function actionDelete()
    {
        $model = System::findOne(['id' => Yii::$app->request->get('id', 0), 'deleted_at' => 0]);
        if (!$model) {
            return $this->redirect('index');
        }

        if (Yii::$app->request->isPost) {
            $model->deleted_at = time();
            $model->save();
        }
        return $this->redirect(['index']);
    }
}