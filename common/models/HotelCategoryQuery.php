<?php

namespace common\models;

use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[HotelCategory]].
 *
 * @see HotelCategory
 */
class HotelCategoryQuery extends ActiveQuery
{
    public function active(): HotelCategoryQuery
    {
        return $this->andWhere('[[deleted_at]] IS NULL');
    }

    /**
     * {@inheritdoc}
     * @return HotelCategory[]|array
     */
    public function all($db = null): array
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return HotelCategory|array|null
     */
    public function one($db = null): array|HotelCategory|null
    {
        return parent::one($db);
    }
}
