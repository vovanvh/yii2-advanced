<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\YiiAsset;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Hotel */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hotels', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
YiiAsset::register($this);

?>
<div class="">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Create New', ['create'], ['class' => 'btn btn-success']) ?>
        <?= Html::a('Back to List', ['index'], ['class' => 'btn btn-info']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'zimmeranzahl',
            [
                'attribute' => 'sterne',
                'value' => function ($model) {
                    return $model->getStarsDisplay();
                },
                'format' => 'raw',
            ],
            [
                'attribute' => 'pool',
                'value' => function ($model) {
                    return $model->pool ? 'Yes' : 'No';
                },
            ],
            [
                'attribute' => 'spa',
                'value' => function ($model) {
                    return $model->spa ? 'Yes' : 'No';
                },
            ],
            [
                'attribute' => 'hotelCategoriesIds',
                'value' => function ($model) {
                    $hotelCategories = ArrayHelper::getColumn($model->hotelCategories, 'name');
                    return count($hotelCategories) ? implode(', ', $hotelCategories) : '';
                },
            ],
            'created_at:datetime',
            'updated_at:datetime',
        ],
    ]) ?>

</div>
