<?php

use yii\db\Migration;

class m250708_065602_hotel_to_hotel_category extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%hotel_categories}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255)->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
            'updated_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP'),
            'deleted_at' => $this->timestamp()->null(),
        ]);

        $this->createTable('{{%hotel_to_hotel_category}}', [
            'hotel_id' => $this->integer()->notNull(),
            'hotel_category_id' => $this->integer()->notNull(),
            'created_at' => $this->timestamp()->defaultExpression('CURRENT_TIMESTAMP'),
        ]);

        // add primary key for hotel_to_hotel_category table
        $this->addPrimaryKey('pk-hotel_to_hotel_category', '{{%hotel_to_hotel_category}}', ['hotel_id', 'hotel_category_id']);

        // Add foreign keys for hotel_to_hotel_category table
        $this->addForeignKey(
            'fk-hotel_to_hotel_category-hotel_id',
            '{{%hotel_to_hotel_category}}',
            'hotel_id',
            '{{%hotels}}',
            'id',
            'CASCADE'
        );

        $this->addForeignKey(
            'fk-hotel_to_hotel_category-hotel_category_id',
            '{{%hotel_to_hotel_category}}',
            'hotel_category_id',
            '{{%hotel_categories}}',
            'id',
            'CASCADE'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        // Drop foreign keys of hotel_to_hotel_category table
        $this->dropForeignKey('fk-hotel_to_hotel_category-hotel_id', '{{%hotel_to_hotel_category}}');
        $this->dropForeignKey('fk-hotel_to_hotel_category-hotel_category_id', '{{%hotel_to_hotel_category}}');

        // Drop primary key of hotel_to_hotel_category table
        $this->dropPrimaryKey('pk-hotel_to_hotel_category', '{{%hotel_to_hotel_category}}');

        // Drop tables
        $this->dropTable('{{%hotel_to_hotel_category}}');
        $this->dropTable('{{%hotel_categories}}');
    }
}
