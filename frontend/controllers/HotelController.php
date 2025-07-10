<?php

namespace frontend\controllers;

use frontend\assets\HotelAsset;
use common\models\Hotel;
use common\models\HotelSearch;
use yii\data\ActiveDataProvider;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class HotelController extends Controller
{
    public function actionIndex(): string
    {
        $searchModel = new HotelSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @throws BadRequestHttpException
     */
    public function beforeAction($action): bool
    {
        if (!parent::beforeAction($action)) {
            return false;
        }

        // Register asset for all actions in this controller
        HotelAsset::register($this->getView());

        return true;
    }

    /**
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    protected function findModel($id)
    {
        if (($model = Hotel::find()->active()->andWhere(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
