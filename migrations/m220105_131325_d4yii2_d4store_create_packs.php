<?php

use yii\db\Migration;

class m220105_131325_d4yii2_d4store_create_packs  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_packs` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `code` char(20) CHARACTER SET utf8 DEFAULT NULL,
              `notes` text CHARACTER SET utf8,
              `is_active` tinyint(3) unsigned NOT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
                    
        ');
    }

    public function safeDown() {
        echo "m220105_131325_d4yii2_d4store_create_packs cannot be reverted.\n";
        return false;
    }
}
