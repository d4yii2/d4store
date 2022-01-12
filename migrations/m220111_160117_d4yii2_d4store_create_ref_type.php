<?php

use yii\db\Migration;

class m220111_160117_d4yii2_d4store_create_ref_type  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_ref_type` (
              `id` tinyint(3) unsigned NOT NULL,
              `name` varchar(20) NOT NULL,
              `sys_model_id` tinyint(3) unsigned NOT NULL,
              `url` text,
              PRIMARY KEY (`id`),
              KEY `d4store_ref_type_ibfk_sys_model` (`sys_model_id`),
              CONSTRAINT `d4store_ref_type_ibfk_sys_model` FOREIGN KEY (`sys_model_id`) REFERENCES `sys_models` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
                    
        ');
    }

    public function safeDown() {
        echo "m220111_160117_d4yii2_d4store_create_ref_type cannot be reverted.\n";
        return false;
    }
}
