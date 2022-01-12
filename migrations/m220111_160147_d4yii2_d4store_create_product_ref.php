<?php

use yii\db\Migration;

class m220111_160147_d4yii2_d4store_create_product_ref  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_product_ref` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `store_product_id` int(10) unsigned NOT NULL,
              `model_record_id` int(10) unsigned NOT NULL,
              `ref_type_id` tinyint(3) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `d4store_product_ref_ibfk_store_product` (`store_product_id`),
              KEY `d4store_product_ref_ibfk_ref_type` (`ref_type_id`),
              CONSTRAINT `d4store_product_ref_ibfk_ref_type` FOREIGN KEY (`ref_type_id`) REFERENCES `d4store_ref_type` (`id`),
              CONSTRAINT `d4store_product_ref_ibfk_store_product` FOREIGN KEY (`store_product_id`) REFERENCES `d4store_store_product` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=latin1
                    
        ');
    }

    public function safeDown() {
        echo "m220111_160147_d4yii2_d4store_create_product_ref cannot be reverted.\n";
        return false;
    }
}
