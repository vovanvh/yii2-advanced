<?php

use common\models\HotelImage;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\widgets\ListView;
use slavkovrn\lightbox\LightBoxWidget;
use slavkovrn\imagecarousel\ImageCarouselWidget;

/* @var $this yii\web\View */
/* @var $model common\models\Hotel */

$images = $model->images;
usort($images, function(HotelImage $a, HotelImage $b)
{
    return strcmp($b->sort_order, $a->sort_order);
});
$imagesLBArray = array_map(function (HotelImage $image) use ($model) {
    return [
        'src' => $image->getUrlForFront(),
        'title' => $image->caption ?? 'image' . $image->id,
    ];
}, $images);
$imagesCarouselArray = array_map(function (HotelImage $image) use ($model) {
    return [
        'src' => $image->getUrlForFront(),
        'alt' => $image->caption ?? 'image' . $image->id,
    ];
}, $images);


$this->params['breadcrumbs'][] = ['label' => 'Hotels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->name;

?>

<div>
    <div class="row">
        <div class="col-lg-12">
            <h1><?= Html::encode($model->name) ?></h1>
        </div>
    </div>
    <div class="row">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'name',
                'zimmeranzahl',
                [
                    'label' => 'Sterne',
                    'value' => $model->getStarsDisplay(),
                ],
                [
                    'label' => 'Pool vorhanden',
                    'value' => $model->pool === 1 ? 'Ja' : 'Nein',
                ],
                [
                    'label' => 'Spa vorhanden',
                    'value' => $model->spa === 1 ? 'Ja' : 'Nein',
                ],
                [
                    'attribute' => 'hotelCategoriesIds',
                    'value' => function ($model) {
                        $hotelCategories = ArrayHelper::getColumn($model->hotelCategories, 'name');
                        return count($hotelCategories) ? implode(', ', $hotelCategories) : '';
                    },
                ],
            ],
        ]); ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>Photos</h3>
        </div>
    </div>
    <div class="row">
        <?= ListView::widget([
            'dataProvider' => (new ArrayDataProvider([
                'allModels' => $images,
            ])),
            'itemOptions' => ['class' => 'col-md-4 col-sm-6 mb-4'],
            'itemView' => '_image_item',
            'layout' => '<div class="row">{items}</div>{pager}',
            'options' => ['class' => 'hotel-list'],
        ]) ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>Photos Light box</h3>
        </div>
    </div>
    <div class="row">
        <?= LightBoxWidget::widget([
            'id' =>'lightbox',
            //'class' =>'galary',
            'height' =>'200px',
            //'width' =>'100px',
            'images' => $imagesLBArray,
        ]); ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <h3>Photos Slider</h3>
        </div>
    </div>
    <div class="row">
        <?= ImageCarouselWidget::widget([
            'id' =>'image-carousel',    // unique id of widget
            'width' => 960,             // width of widget container
            'height' => 300,            // height of widget container
            'img_width' => 320,         // width of central image
            'img_height' => 180,        // height of central image
            'images' => $imagesCarouselArray
        ]); ?>
    </div>
</div>