<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * This is the model class for table "hotel_to_hotel_category".
 *
 * @property int $hotel_id
 * @property int $hotel_category_id
 * @property string $created_at
 *
 * @property Hotel $hotel
 * @property HotelCategory $hotelCategory
 */
class HotelToHotelCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{hotel_to_hotel_category}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules(): array
    {
        return [
            [['hotel_id', 'hotel_category_id'], 'required'],
            [['hotel_id', 'hotel_category_id'], 'integer'],
            [['created_at'], 'safe'],
            [['hotel_id', 'hotel_category_id'], 'unique', 'targetAttribute' => ['hotel_id', 'hotel_category_id']],
            [['hotel_category_id'], 'exist', 'skipOnError' => true, 'targetClass' => HotelCategory::class, 'targetAttribute' => ['hotel_category_id' => 'id']],
            [['hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hotel_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'hotel_id' => 'Hotel ID',
            'hotel_category_id' => 'Hotel Category ID',
            'created_at' => 'Created At',
        ];
    }

    /**
     * Gets query for [[Hotel]].
     *
     * @return ActiveQuery
     */
    public function getHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['id' => 'hotel_id']);
    }

    /**
     * Gets query for [[HotelCategory]].
     *
     * @return ActiveQuery
     */
    public function getHotelCategory(): ActiveQuery
    {
        return $this->hasOne(HotelCategory::class, ['id' => 'hotel_category_id']);
    }

}
