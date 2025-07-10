<?php

namespace backend\controllers;

use common\models\HotelCategory;
use common\models\HotelCategorySearch;
use yii\db\Exception;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\Response;

/**
 * HotelCategoryController implements the CRUD actions for HotelCategory model.
 */
class HotelCategoryController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors(): array
    {
        return array_merge(
            parent::behaviors(),
            [
                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all HotelCategory models.
     *
     * @return string
     */
    public function actionIndex(): string
    {
        $searchModel = new HotelCategorySearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single HotelCategory model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView(int $id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new HotelCategory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(): Response|string
    {
        $model = new HotelCategory();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing HotelCategory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|Response
     * @throws NotFoundHttpException|Exception if the model cannot be found
     */
    public function actionUpdate(int $id): Response|string
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing HotelCategory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return Response
     * @throws NotFoundHttpException|Exception if the model cannot be found
     */
    public function actionDelete(int $id): Response
    {
        $this->findModel($id)->softDelete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the HotelCategory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return HotelCategory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel(int $id): HotelCategory
    {
        if (($model = HotelCategory::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
