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
 * Hotel model with image relationship
 * @property HotelImage $displayImage
 * @property int $imageCount
 * @property HotelImage $mainImage
 * @property HotelImage $firstImage
 * @property int $sterne
 * @property string $name
 * @property int $pool
 * @property int $spa
 * @property int $zimmeranzahl
 * @property HotelImage[] $images
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 * @property HotelCategory[] $hotelCategories
 * @property Expression $location
 */
class Hotel extends ActiveRecord
{
    public array $hotelCategoriesIds = [];
    public $latitude;
    public $longitude;

    public static function tableName(): string
    {
        return '{{%hotels}}';
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

    public function rules(): array
    {
        return [
            [['name', 'zimmeranzahl'], 'required'],
            [['zimmeranzahl', 'sterne'], 'integer'],
            [['sterne'], 'in', 'range' => [1, 2, 3, 4, 5]],
            [['pool', 'spa'], 'boolean'],
            [['name'], 'string', 'max' => 255],
            [['created_at', 'updated_at', 'deleted_at'], 'safe'],
            [['location'], 'safe'],
            [['latitude', 'longitude'], 'number'],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'name' => 'Hotel Name',
            'zimmeranzahl' => 'Zimmeranzahl',
            'sterne' => 'Sterne',
            'pool' => 'Pool',
            'spa' => 'Spa',
            'created_at' => 'Created',
            'updated_at' => 'Updated',
            'deleted_at' => 'Deleted',
            'hotelCategoriesIds' => 'Categories',
        ];
    }

    public function beforeSave($insert): bool
    {
        if ($this->latitude !== null && $this->longitude !== null) {
            $this->location = new Expression("POINT(:lon, :lat)", [
                ':lon' => $this->longitude,
                ':lat' => $this->latitude
            ]);
        } else {
            $this->location = new Expression("POINT(0, 0)");
        }

        return parent::beforeSave($insert);
    }

    /**
     * @throws Exception
     */
    public function afterFind(): void
    {
        parent::afterFind();
        if ($this->location !== null) {
            $this->latitude = Yii::$app->db->createCommand("SELECT ST_Y(location) FROM hotels WHERE id = :id")
                ->bindValue(':id', $this->id)
                ->queryScalar();
            $this->longitude = Yii::$app->db->createCommand("SELECT ST_X(location) FROM hotels WHERE id = :id")
                ->bindValue(':id', $this->id)
                ->queryScalar();
        }
    }

    /**
     * Get all images for this hotel
     */
    public function getImages(): ActiveQuery
    {
        return $this->hasMany(HotelImage::class, ['hotel_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_ASC]);
    }

    /**
     * Get main image for this hotel
     */
    public function getMainImage(): ActiveQuery
    {
        return $this->hasOne(HotelImage::class, ['hotel_id' => 'id'])
            ->where(['is_main' => true]);
    }

    /**
     * Get first image if no main image is set
     */
    public function getFirstImage(): ActiveQuery
    {
        return $this->hasOne(HotelImage::class, ['hotel_id' => 'id'])
            ->orderBy(['sort_order' => SORT_ASC, 'created_at' => SORT_ASC]);
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
     * Gets query for [[HotelCategories]].
     *
     * @return ActiveQuery|HotelCategoryQuery
     * @throws InvalidConfigException
     */
    public function getHotelCategories(): HotelCategoryQuery|ActiveQuery
    {
        return $this->hasMany(HotelCategory::class, ['id' => 'hotel_category_id'])
            ->alias('hc')
            ->where(['hc.deleted_at' => null])
            ->viaTable('hotel_to_hotel_category', ['hotel_id' => 'id']);
    }

    /**
     * Get display image (main or first)
     */
    public function getDisplayImage(): HotelImage
    {
        return $this->mainImage ?: $this->firstImage;
    }

    /**
     * Get stars display
     */
    public function getStarsDisplay(): string
    {
        return str_repeat('★', $this->sterne) . str_repeat('☆', 5 - $this->sterne);
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

    /**
     * @throws Exception
     */
    public function restore(): bool
    {
        $this->deleted_at = null;
        return $this->save(false);
    }

    /**
     * Scope for active hotels
     */
    public static function find(): HotelQuery
    {
        return new HotelQuery(get_called_class());
    }

    /**
     * Get image count
     */
    public function getImageCount(): bool|int|string|null
    {
        return $this->getImages()->count();
    }

    /**
     * @throws Exception
     */
    public function saveHotelCategories(): void
    {
        \Yii::$app->db->createCommand()
            ->delete('hotel_to_hotel_category', ['hotel_id' => $this->id])
            ->execute();

        if (count($this->hotelCategoriesIds)) {
            foreach ($this->hotelCategoriesIds as $hotelCategoriesId) {
                \Yii::$app->db->createCommand()
                    ->insert('hotel_to_hotel_category', [
                        'hotel_id' => $this->id,
                        'hotel_category_id' => $hotelCategoriesId
                    ])
                    ->execute();
            }
        }
    }

    public function setLocationFromCoords($lat, $lon): void
    {
        $this->location = new Expression("POINT(:lon, :lat)", [
            ':lon' => $lon,
            ':lat' => $lat
        ]);
    }
}
