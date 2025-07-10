<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\HotelCategory $model */

$this->title = 'Create Hotel Category';
$this->params['breadcrumbs'][] = ['label' => 'Hotel Categories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hotel-category-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
