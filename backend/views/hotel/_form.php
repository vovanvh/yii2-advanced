<?php

use common\models\HotelCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model common\models\Hotel */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="hotel-form">

    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data']
    ]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'zimmeranzahl')->textInput(['type' => 'number', 'min' => 1]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'hotelCategoriesIds')->widget(Select2::class, [
                'data' => ArrayHelper::map(HotelCategory::find()->active()->all(), 'id', 'name'),
                'name' => 'hotel_categories_ids',
                'options' => ['multiple' => true, 'placeholder' => 'Select categories ...'],
            ]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'sterne')->dropDownList(
                Yii::$app->params['hotels.stars'],
                ['prompt' => 'Select star rating']
            ) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'pool')->checkbox() ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'spa')->checkbox() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'latitude')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'longitude')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <!-- Image Upload Section -->
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Hotel Images</label>
                <div class="image-upload-container">
                    <?= Html::fileInput('hotel_images[]', null, [
                        'multiple' => true,
                        'accept' => 'image/*',
                        'class' => 'form-control',
                        'id' => 'hotel-images-input',
                        'style' => 'margin-bottom: 10px;'
                    ]) ?>
                    <div class="help-block">
                        <small>Select multiple images (JPG, PNG, GIF). Maximum 10 images, 5MB each.</small>
                    </div>
                </div>
            </div>

            <div id="image-preview-container" class="row" style="margin-top: 15px;"></div>
        </div>
    </div>

    <?php if (!$model->isNewRecord && $model->images): ?>
        <div class="row">
            <div class="col-md-12">
                <h4>Current Images</h4>
                <div class="row">
                    <?php foreach ($model->images as $image): ?>
                        <div class="col-md-6 col-sm-6 col-xs-6" style="margin-bottom: 15px;">
                            <div class="thumbnail">
                                <?= Html::img('@web/uploads/hotels/' . $image->filename, [
                                    'alt' => $image->alt_text,
                                    'class' => 'img-responsive',
                                    'style' => 'height: 150px; object-fit: cover; width: 100%;'
                                ]) ?>
                                <div class="caption">
                                    <p><small><?= Html::encode($image->original_name) ?></small></p>
                                    <div class="btn-group btn-group-xs">
                                        <?= Html::checkbox('main_image', $image->is_main, [
                                            'value' => $image->id,
                                            'label' => 'Main',
                                            'class' => 'main-image-checkbox'
                                        ]) ?>
                                        <?= Html::checkbox('delete_images[]', false, [
                                            'value' => $image->id,
                                            'label' => 'Delete',
                                            'class' => 'delete-image-checkbox'
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        <?= Html::a('Cancel', ['index'], ['class' => 'btn btn-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
