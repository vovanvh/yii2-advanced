<?php

namespace backend\controllers;

use backend\assets\HotelAsset;
use common\models\Hotel;
use common\models\HotelImage;
use common\models\HotelSearch;
use Yii;
use yii\db\Exception;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\FileHelper;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\UploadedFile;

class HotelController extends Controller
{
    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
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
     * @throws NotFoundHttpException
     */
    public function actionView($id): string
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    public function actionCreate(): Response|string
    {
        /*if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            // array(3) { [0]=> string(3) "111" [1]=> string(3) "333" [2]=> string(3) "555" } array(3) { [0]=> string(3) "222" [1]=> string(3) "444" [2]=> string(3) "666" } string(1) "2" string(73) "
            var_dump($post['image_alt_text'], $post['image_caption'], $post['main_image_new']);
            var_dump(' ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ===== ');
            var_dump(UploadedFile::getInstancesByName('hotel_images'));
            die();
        }*/
        $model = new Hotel();

        if ($model->load(Yii::$app->request->post())) {
            $model->hotelCategoriesIds = Yii::$app->request->post('Hotel')['hotelCategoriesIds'];

            if ($model->save()) {
                if (count($model->hotelCategoriesIds)) {
                    $model->saveHotelCategories();
                }
                $this->uploadFiles($model);

                Yii::$app->session->setFlash('success', 'Hotel created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException|\yii\base\Exception
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);
        $model->hotelCategoriesIds = ArrayHelper::getColumn($model->hotelCategories, 'id');

        if ($model->load(Yii::$app->request->post())) {
            $model->hotelCategoriesIds = Yii::$app->request->post('Hotel')['hotelCategoriesIds'];

            if ($model->save()) {
                if (count($model->hotelCategoriesIds)) {
                    $model->saveHotelCategories();
                }
                $this->uploadFiles($model);

                Yii::$app->session->setFlash('success', 'Hotel updated successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionDelete($id): Response
    {
        $this->findModel($id)->softDelete();
        Yii::$app->session->setFlash('success', 'Hotel deleted successfully.');
        return $this->redirect(['index']);
    }

    /**
     * @throws \yii\base\Exception
     */
    public function actionImport(): array
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        if (Yii::$app->request->isPost) {
            $csvFile = UploadedFile::getInstanceByName('csv_file');

            if ($csvFile === null) {
                return ['success' => false, 'message' => 'Please select a CSV file.'];
            }

            if ($csvFile->extension !== 'csv') {
                return ['success' => false, 'message' => 'Please upload a valid CSV file.'];
            }

            $hasHeader = Yii::$app->request->post('has_header', false);
            $delimiter = Yii::$app->request->post('delimiter', ',');

            if ($delimiter === '\t') {
                $delimiter = "\t";
            }

            try {
                $handle = fopen($csvFile->tempName, 'r');

                if ($handle === false) {
                    return ['success' => false, 'message' => 'Could not read the CSV file.'];
                }

                $rowCount = 0;
                $importedCount = 0;
                $errors = [];

                if ($hasHeader) {
                    fgetcsv($handle, 0, $delimiter);
                }

                $transaction = Yii::$app->db->beginTransaction();

                try {
                    while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
                        $rowCount++;

                        if (empty(array_filter($row))) {
                            continue;
                        }

                        $model = new Hotel();
                        $images = [];

                        if (isset($row[0])) $model->name = trim($row[0]);
                        if (isset($row[1])) $model->zimmeranzahl = trim($row[1]);
                        if (isset($row[2])) $model->sterne = trim($row[2]);
                        if (isset($row[3])) $model->pool = intval(trim($row[3])) > 0 ? 1 : 0;
                        if (isset($row[4])) $model->spa = intval(trim($row[4])) > 0 ? 1 : 0;
                        if (isset($row[5])) $model->hotelCategoriesIds = explode(',', trim($row[5]));
                        if (isset($row[6])) $images = explode(',', trim($row[6]));

                        if ($model->validate()) {
                            if ($model->save()) {
                                $model->saveHotelCategories();
                                $importedCount++;
                                if (count($images)) {
                                    $uploadPath = Yii::getAlias('@webroot') . '/' . HotelImage::UPLOAD_PATH;
                                    FileHelper::createDirectory($uploadPath);

                                    foreach ($images as $url) {
                                        if (!filter_var($url, FILTER_VALIDATE_URL)) {
                                            Yii::warning("Invalid URL: {$url}");
                                            continue;
                                        }

                                        $ch = curl_init();
                                        curl_setopt($ch, CURLOPT_URL, $url);
                                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                                        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
                                        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; YourApp/1.0)');
                                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                                        $imageData = curl_exec($ch);
                                        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
                                        $fileSize = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);

                                        if (curl_errno($ch)) {
                                            Yii::error("cURL error for {$url}: " . curl_error($ch));
                                            curl_close($ch);
                                            continue;
                                        }

                                        curl_close($ch);

                                        if ($httpCode !== 200 || $imageData === false) {
                                            Yii::error("Failed to download image from {$url}. HTTP Code: {$httpCode}");
                                            continue;
                                        }

                                        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                                        if (!in_array($contentType, $allowedTypes)) {
                                            Yii::warning("Unsupported content type {$contentType} for {$url}");
                                            continue;
                                        }

                                        $extension = $this->getExtensionFromMimeType($contentType);
                                        $filename = uniqid() . '.' . $extension;
                                        $filePath = $uploadPath . $filename;

                                        if (file_put_contents($filePath, $imageData) === false) {
                                            Yii::error("Failed to save image to {$filePath}");
                                            continue;
                                        }

                                        $imageInfo = getimagesize($filePath);
                                        if ($imageInfo === false) {
                                            Yii::error("Invalid image file: {$filePath}");
                                            unlink($filePath); // Delete invalid file
                                            continue;
                                        }

                                        $originalName = basename(parse_url($url, PHP_URL_PATH));
                                        if (empty($originalName) || strpos($originalName, '.') === false) {
                                            $originalName = $filename;
                                        }

                                        $hotel_id = $model->getPrimaryKey();
                                        $hotelImage = new HotelImage([
                                            'hotel_id' => $hotel_id,
                                            'filename' => $filename,
                                            'original_name' => $originalName,
                                            'file_size' => $fileSize > 0 ? $fileSize : filesize($filePath),
                                            'width' => $imageInfo[0] ?? null,
                                            'height' => $imageInfo[1] ?? null,
                                            'mime_type' => $contentType,
                                            'sort_order' => HotelImage::find()->where(['hotel_id' => $hotel_id])->max('sort_order') + 1,
                                        ]);

                                        // Set as main image if it's the first image
                                        if (HotelImage::find()->where(['hotel_id' => $hotel_id])->count() == 0) {
                                            $hotelImage->is_main = true;
                                        }

                                        if ($hotelImage->save()) {
                                            Yii::info("Successfully downloaded and saved image from {$url}");
                                        } else {
                                            Yii::error("Failed to save HotelImage model for {$url}: " . json_encode($hotelImage->errors));
                                            unlink($filePath); // Clean up file if model save failed
                                        }
                                    }
                                }
                            } else {
                                $errors[] = "Row {$rowCount}: Failed to save record.";
                            }
                        } else {
                            $errorMessages = [];
                            foreach ($model->errors as $field => $fieldErrors) {
                                $errorMessages[] = $field . ': ' . implode(', ', $fieldErrors);
                            }
                            $errors[] = "Row {$rowCount}: " . implode('; ', $errorMessages);
                        }
                    }

                    fclose($handle);

                    if ($importedCount > 0) {
                        $transaction->commit();

                        $message = "Successfully imported {$importedCount} records.";
                        if (!empty($errors)) {
                            $message .= " " . count($errors) . " rows had errors.";
                        }

                        return [
                            'success' => true,
                            'message' => $message,
                            'imported' => $importedCount,
                            'errors' => $errors
                        ];
                    } else {
                        $transaction->rollBack();
                        return [
                            'success' => false,
                            'message' => 'No records were imported. Please check your CSV file format.',
                            'errors' => $errors
                        ];
                    }

                } catch (Exception $e) {
                    $transaction->rollBack();
                    throw $e;
                }

            } catch (Exception $e) {
                if (isset($handle)) {
                    fclose($handle);
                }

                return [
                    'success' => false,
                    'message' => 'An error occurred while processing the file: ' . $e->getMessage()
                ];
            }
        }

        return ['success' => false, 'message' => 'Invalid request.'];
    }

    /**
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?Hotel
    {
        if (($model = Hotel::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * @throws Exception
     * @throws \yii\base\Exception
     */
    private function uploadFiles(Hotel $hotel): void
    {
        $uploadedFiles = UploadedFile::getInstancesByName('hotel_images');

        if (!empty($uploadedFiles)) {
            $uploadPath = Yii::getAlias('@webroot') . '/' . HotelImage::UPLOAD_PATH;
            FileHelper::createDirectory($uploadPath);

            foreach ($uploadedFiles as $file) {
                $filename = uniqid() . '.' . $file->extension;
                $filePath = $uploadPath . $filename;

                if ($file->saveAs($filePath)) {
                    // Get image dimensions
                    $imageInfo = getimagesize($filePath);

                    $hotel_id = $hotel->getPrimaryKey();
                    $hotelImage = new HotelImage([
                        'hotel_id' => $hotel_id,
                        'filename' => $filename,
                        'original_name' => $file->baseName . '.' . $file->extension,
                        'file_size' => $file->size,
                        'width' => $imageInfo[0] ?? null,
                        'height' => $imageInfo[1] ?? null,
                        'mime_type' => $file->type,
                        'sort_order' => HotelImage::find()->where(['hotel_id' => $hotel_id])->max('sort_order') + 1,
                    ]);

                    // Set as main image if it's the first image
                    if (HotelImage::find()->where(['hotel_id' => $hotel_id])->count() == 0) {
                        $hotelImage->is_main = true;
                    }

                    $hotelImage->save();
                }
            }

            Yii::$app->session->setFlash('success', 'Images uploaded successfully.');
        }
    }

    private function getExtensionFromMimeType(string $mimeType): string
    {
        $extensions = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'image/gif' => 'gif',
            'image/webp' => 'webp',
        ];

        return $extensions[$mimeType] ?? 'jpg';
    }
}
