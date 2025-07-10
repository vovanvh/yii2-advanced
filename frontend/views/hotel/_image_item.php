<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\HotelImage */

?>

<div class="card h-100">
    <img src="<?= $model->getUrlForFront() ?>"
        class="card-img-top"
        alt="<?= Html::encode($model->alt_text ?: '') ?>"
        style="height: 200px; object-fit: cover;">
</div>
