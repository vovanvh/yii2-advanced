<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Hotel */

$hotelCategories = ArrayHelper::getColumn($model->hotelCategories, 'name');

?>

<div class="card h-100">
    <?php if ($model->displayImage): ?>
        <img src="<?= $model->displayImage->getUrlForFront() ?>"
             class="card-img-top"
             alt="<?= Html::encode($model->displayImage->alt_text ?: $model->name) ?>"
             style="height: 200px; object-fit: cover;">
    <?php else: ?>
        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
            <span class="text-muted">No Image</span>
        </div>
    <?php endif; ?>

    <div class="card-body">
        <h5 class="card-title"><?= Html::encode($model->name) ?></h5>
        <p class="card-text">
            <strong>Zimmeranzahl:</strong> <?= $model->zimmeranzahl ?><br>
            <strong>Sterne:</strong> <?= $model->getStarsDisplay() ?><br>
            <strong>Pool:</strong> <?= $model->pool ? 'Yes' : 'No' ?><br>
            <strong>Spa:</strong> <?= $model->spa ? 'Yes' : 'No' ?><br>
            <?php if (count($hotelCategories)): ?>
                <strong>Categories:</strong> <?= implode(', ', $hotelCategories) ?>
            <?php endif ?>
        </p>
        <?php if ($model->imageCount > 1): ?>
            <small class="text-muted"><?= $model->imageCount ?> images</small>
        <?php endif; ?>
    </div>
    <div class="card-footer">
        <?= Html::a('View Details', ['view', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </div>
</div>
