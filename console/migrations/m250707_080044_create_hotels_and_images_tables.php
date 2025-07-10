<?php

use yii\db\Migration;

class m250707_080044_create_hotels_and_images_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hotels}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'zimmeranzahl' => $this->integer()->notNull(),
            'sterne' => $this->integer()->notNull(),
            'pool' => $this->boolean()->notNull(),
            'spa' => $this->boolean()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('{{%hotel_images}}', [
            'id' => $this->primaryKey(),
            'hotel_id' => $this->integer()->notNull(),
            'filename' => $this->string(255)->notNull(),
            'original_name' => $this->string(255)->notNull(),
            'alt_text' => $this->string(255)->null(),
            'caption' => $this->text()->null(),
            'file_size' => $this->integer()->null(),
            'width' => $this->integer()->null(),
            'height' => $this->integer()->null(),
            'mime_type' => $this->string(100)->null(),
            'sort_order' => $this->integer()->defaultValue(0),
            'is_main' => $this->boolean()->defaultValue(false),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
        ]);

        // Foreign key constraint
        $this->addForeignKey(
            'fk-hotel_images-hotel_id',
            '{{%hotel_images}}',
            'hotel_id',
            '{{%hotels}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-hotel_images-hotel_id', '{{%hotel_images}}');
        $this->dropTable('{{%hotels}}');
        $this->dropTable('{{%hotel_images}}');
    }
}
