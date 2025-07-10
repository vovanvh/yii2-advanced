<?php

use common\models\HotelCategory;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ListView;
use yii\widgets\ActiveForm;
use yii\widgets\Pjax;
use kartik\select2\Select2;
use kartik\select2\Select2Asset;

/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $this yii\web\View */
/** @var common\models\HotelSearch $searchModel */

$this->title = 'Hotels';
$this->params['breadcrumbs'][] = $this->title;

Select2Asset::register($this);

?>
<div class="">
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($this->title) ?></h1>
        </div>
    </div>

    <?php Pjax::begin(['id' => 'hotel-filter', 'timeout' => 5000, 'enablePushState' => false]); ?>

    <div class="mb-4">
        <?php $form = ActiveForm::begin([
            'method' => 'get',
            'action' => ['hotel/index'],
            'options' => ['data-pjax' => 1, 'id' => 'filter-form'],
        ]); ?>

        <div class="row">

            <div class="col-md-6"><?=
                $form->field($searchModel, 'sterne')->widget(Select2::class, [
                    'data' => Yii::$app->params['hotels.stars'],
                    'name' => 'hotel_stars',
                    'pjaxContainerId' => 'hotel-filter',
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Select stars ...',
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function() { document.getElementById('filter-form').submit(); }",
                        "select2:unselect" => "function() { document.getElementById('filter-form').submit(); }",
                    ],
                ])
            ?></div>

            <div class="col-md-6"><?=
                $form->field($searchModel, 'searchByHotelCategories')->widget(Select2::class, [
                    'data' => ArrayHelper::map(HotelCategory::find()->active()->all(), 'id', 'name'),
                    'name' => 'hotel_categories_ids',
                    'pjaxContainerId' => 'hotel-filter',
                    'options' => [
                        'multiple' => true,
                        'placeholder' => 'Select categories ...',
                    ],
                    'pluginEvents' => [
                        "select2:select" => "function() { document.getElementById('filter-form').submit(); }",
                        "select2:unselect" => "function() { document.getElementById('filter-form').submit(); }",
                    ],
                ])
            ?></div>
        </div>

        <?php ActiveForm::end(); ?>
    </div>

    <?= ListView::widget([
        'dataProvider' => $dataProvider,
        'itemOptions' => ['class' => 'col-md-4 col-sm-6 mb-4'],
        'itemView' => '_hotel_item',
        'layout' => '<div class="row">{items}</div>{pager}',
        'options' => ['class' => 'hotel-list'],
    ]) ?>

    <?php Pjax::end(); ?>
</div>
