<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * HotelImage model
 *
 * @property int $id
 * @property int $hotel_id
 * @property string $filename
 * @property string $original_name
 * @property string $alt_text
 * @property string $caption
 * @property int $file_size
 * @property int $width
 * @property int $height
 * @property string $mime_type
 * @property int $sort_order
 * @property bool $is_main
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Hotel $hotel
 */
class HotelImage extends ActiveRecord
{
    const UPLOAD_PATH = 'uploads/hotels/';

    public static function tableName(): string
    {
        return '{{%hotel_images}}';
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
            [['hotel_id', 'filename', 'original_name'], 'required'],
            [['hotel_id', 'file_size', 'width', 'height', 'sort_order'], 'integer'],
            [['is_main'], 'boolean'],
            [['caption'], 'string'],
            [['filename', 'original_name', 'alt_text'], 'string', 'max' => 255],
            [['mime_type'], 'string', 'max' => 100],
            [['created_at', 'updated_at'], 'safe'],
            [['hotel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Hotel::class, 'targetAttribute' => ['hotel_id' => 'id']],
        ];
    }

    public function attributeLabels(): array
    {
        return [
            'id' => 'ID',
            'hotel_id' => 'Hotel ID',
            'filename' => 'Filename',
            'original_name' => 'Original Name',
            'alt_text' => 'Alt Text',
            'caption' => 'Caption',
            'file_size' => 'File Size',
            'width' => 'Width',
            'height' => 'Height',
            'mime_type' => 'Mime Type',
            'sort_order' => 'Sort Order',
            'is_main' => 'Main Image',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Gets query for [[Hotel]].
     */
    public function getHotel(): ActiveQuery
    {
        return $this->hasOne(Hotel::class, ['id' => 'hotel_id']);
    }

    /**
     * Get full URL to image
     */
    public function getUrl(): string
    {
        return Yii::getAlias('@web') . '/' . self::UPLOAD_PATH . $this->filename;
    }

    public function getUrlForFront(): string
    {
        return Yii::$app->params['domain'] . '/' . Yii::$app->params['imagesUri'] . '/' . self::UPLOAD_PATH . $this->filename;
    }

    /**
     * Get full path to image
     */
    public function getPath(): string
    {
        return Yii::getAlias('@webroot') . '/' . self::UPLOAD_PATH . $this->filename;
    }

    /**
     * Get thumbnail URL (if thumbnails are implemented)
     */
    public function getThumbnailUrl(): string
    {
        $pathInfo = pathinfo($this->filename);
        $thumbnailFilename = $pathInfo['filename'] . '_thumb.' . $pathInfo['extension'];
        return Yii::getAlias('@web') . '/' . self::UPLOAD_PATH . 'thumbs/' . $thumbnailFilename;
    }

    /**
     * Format file size
     */
    public function getFormattedFileSize(): ?string
    {
        return Yii::$app->formatter->asShortSize($this->file_size);
    }

    /**
     * Delete image file when model is deleted
     */
    public function afterDelete(): void
    {
        parent::afterDelete();
        if (file_exists($this->getPath())) {
            unlink($this->getPath());
        }
    }

    /**
     * Ensure only one main image per hotel
     */
    public function beforeSave($insert): bool
    {
        if (parent::beforeSave($insert)) {
            if ($this->is_main) {
                // Remove main flag from other images of the same hotel
                self::updateAll(['is_main' => false], ['hotel_id' => $this->hotel_id]);
            }
            return true;
        }
        return false;
    }
}
