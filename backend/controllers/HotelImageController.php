<?php

namespace backend\controllers;

use common\models\HotelImage;
use Throwable;
use Yii;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\imagine\Image;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class HotelImageController extends Controller
{
    /**
     * @throws StaleObjectException
     * @throws Throwable
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $image = $this->findModel($id);
        $hotel_id = $image->hotel_id;

        $image->delete();
        Yii::$app->session->setFlash('success', 'Image deleted successfully.');

        return $this->redirect(['hotel/view', 'id' => $hotel_id]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->session->setFlash('success', 'Image updated successfully.');
            return $this->redirect(['hotel/view', 'id' => $model->hotel_id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    protected function findModel($id): ?HotelImage
    {
        if (($model = HotelImage::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
