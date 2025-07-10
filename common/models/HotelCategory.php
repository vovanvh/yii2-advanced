<?php

namespace common\models;

use Yii;
use yii\base\InvalidConfigException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Exception;
use yii\db\Expression;

/**
 * This is the model class for table "hotel_categories".
 *
 * @property int $id
 * @property int $name
 * @property string $created_at
 * @property string $updated_at
 * @property string|null $deleted_at
 *
 * @property HotelToHotelCategory[] $hotelToHotelCategories
 * @property Hotel[] $hotels
 */
class HotelCategory extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName(): string
    {
        return '{{hotel_categories}}';
    }

    public function behaviors(): array
    {
        return [
            [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
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
            [['deleted_at'], 'default', 'value' => null],
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'deleted_at' => 'Deleted At',
        ];
    }

    /**
     * Gets query for [[HotelToHotelCategories]].
     *
     * @return ActiveQuery
     */
    public function getHotelToHotelCategories(): ActiveQuery
    {
        return $this->hasMany(HotelToHotelCategory::class, ['hotel_category_id' => 'id']);
    }

    /**
     * Gets query for [[Hotels]].
     *
     * @return ActiveQuery|HotelQuery
     * @throws InvalidConfigException
     */
    public function getHotels(): HotelQuery|ActiveQuery
    {
        return $this->hasMany(Hotel::class, ['id' => 'hotel_id'])
            ->viaTable('hotel_to_hotel_category', ['hotel_category_id' => 'id']);
    }

    /**
     * {@inheritdoc}
     * @return HotelCategoryQuery the active query used by this AR class.
     */
    public static function find(): HotelCategoryQuery
    {
        return new HotelCategoryQuery(get_called_class());
    }

    /**
     * Soft delete functionality
     * @throws Exception
     */
    public function softDelete(): bool
    {
        $this->deleted_at = new Expression('NOW()');
        return $this->save(false);
    }
}
