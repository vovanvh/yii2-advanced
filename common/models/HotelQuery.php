<?php

namespace common\models;

use yii\db\ActiveQuery;

class HotelQuery extends ActiveQuery
{
    public function active(): HotelQuery
    {
        return $this->andWhere(['deleted_at' => null]);
    }

    public function deleted(): HotelQuery
    {
        return $this->andWhere(['not', ['deleted_at' => null]]);
    }

    public function withPool(): HotelQuery
    {
        return $this->andWhere(['pool' => true]);
    }

    public function withSpa(): HotelQuery
    {
        return $this->andWhere(['spa' => true]);
    }

    public function byStars($stars): HotelQuery
    {
        return $this->andWhere(['sterne' => $stars]);
    }
}
