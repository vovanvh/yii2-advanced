<?php

use yii\db\Migration;

class m250709_141703_add_hotel_coordinates extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("ALTER TABLE hotels ADD COLUMN location POINT NOT NULL DEFAULT POINT(0, 0)");

        $this->execute("CREATE SPATIAL INDEX idx_location ON hotels (location)");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("ALTER TABLE hotels DROP INDEX idx_location");
        $this->dropColumn('hotels', 'location');
    }
}
