<?php

use yii\db\Migration;

class m250318_202306_d4yii2_d4store_lists_create  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_lists` (
              `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
              `created` DATETIME NOT NULL COMMENT \'Created\',
              `name` VARCHAR (200) CHARSET utf8 NOT NULL COMMENT \'Name\',
              `notes` TEXT CHARSET utf8 COMMENT \'Notes\',
              PRIMARY KEY (`id`)
            );        
        ');
    }

    public function safeDown() {
        echo "m250318_202306_d4yii2_d4store_lists_create cannot be reverted.\n";
        return false;
    }
}
