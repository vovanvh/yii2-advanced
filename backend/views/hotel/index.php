<?php

use common\models\Hotel;
use common\models\HotelCategory;
use kartik\select2\Select2;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap5\Modal;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/** @var common\models\HotelSearch $searchModel */

$this->title = 'Hotels';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Create Hotel', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::button(
            'Import Hotels (CSV)',
            [
                'class' => 'btn btn-info',
                'data-bs-toggle' => 'modal',
                'data-bs-target' => '#csv-import-modal'
            ]
        ) ?>
    </p>

    <?php Pjax::begin(); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            'zimmeranzahl',
            [
                'attribute' => 'sterne',
                'value' => function ($model) {
                    return $model->getStarsDisplay();
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 120px;'],
            ],
            [
                'attribute' => 'pool',
                'value' => function ($model) {
                    return $model->pool ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-warning">Nein</span>';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
            ],
            [
                'attribute' => 'spa',
                'value' => function ($model) {
                    return $model->spa ? '<span class="badge bg-success">Ja</span>' : '<span class="badge bg-warning">Nein</span>';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 80px; text-align: center;'],
            ],
            [
                'attribute' => 'searchByHotelCategories',
                'label' => Yii::t('app', 'Hotel Categories'),
                'value' => function ($model) {
                    $hotelCategories = ArrayHelper::getColumn($model->hotelCategories, 'name');
                    return count($hotelCategories) ? implode(', ', $hotelCategories) : '';
                },
                'format' => 'raw',
                'contentOptions' => ['style' => 'width: 150px;'],
                'filter' => Select2::widget([
                    'model' => $searchModel,
                    'attribute' => 'searchByHotelCategories',
                    'data' => ArrayHelper::map(HotelCategory::find()->active()->all(), 'id', 'name'),
                    'options' => ['placeholder' => 'Select category...', 'multiple' => true],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
            ],
            [
                'attribute' => 'created_at',
                'format' => 'datetime',
                'contentOptions' => ['style' => 'width: 150px;'],
            ],
            [
                'class' => ActionColumn::class,
                'urlCreator' => function ($action, Hotel $model, $key, $index, $column) {
                    return Url::toRoute([$action, 'id' => $model->id]);
                }
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<?php
// CSV Import Modal
Modal::begin([
    'id' => 'csv-import-modal',
    'title' => 'Import from CSV File',
    'size' => Modal::SIZE_DEFAULT,
]);
?>

    <div class="csv-import-form">
        <?= Html::beginForm(['import'], 'post', ['enctype' => 'multipart/form-data', 'id' => 'csv-import-form']) ?>

        <div class="form-group">
            <label for="csv-file">Select CSV File:</label>
            <?= Html::fileInput('csv_file', null, [
                'class' => 'form-control',
                'id' => 'csv-file',
                'accept' => '.csv',
                'required' => true
            ]) ?>
            <small class="form-text text-muted">
                Please upload a CSV file.
            </small>
        </div>

        <div class="form-group">
            <label>
                <?= Html::checkbox('has_header', true, ['id' => 'has-header']) ?>
                First row contains headers
            </label>
        </div>

        <div class="form-group">
            <label for="delimiter">Delimiter:</label>
            <?= Html::dropDownList('delimiter', ';', [
                //',' => 'Comma (,)',
                ';' => 'Semicolon (;)',
                '\t' => 'Tab',
                '|' => 'Pipe (|)'
            ], ['class' => 'form-control', 'id' => 'delimiter']) ?>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <?= Html::submitButton('Import', ['class' => 'btn btn-primary']) ?>
        </div>

        <?= Html::endForm() ?>
    </div>

<?php Modal::end(); ?>
