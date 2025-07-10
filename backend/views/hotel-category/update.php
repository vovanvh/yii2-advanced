<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\HotelCategory $model */

$this->title = 'Update Hotel Category: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Hotel Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="hotel-category-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
