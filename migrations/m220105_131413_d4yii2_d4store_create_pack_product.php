<?php

use yii\db\Migration;

class m220105_131413_d4yii2_d4store_create_pack_product  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_pack_product` (
              `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
              `store_product_id` int(10) unsigned NOT NULL,
              `pack_id` int(10) unsigned NOT NULL,
              PRIMARY KEY (`id`),
              KEY `d4store_pack_product_ibfk_pack` (`pack_id`),
              KEY `d4store_pack_product_ibfk_store_product` (`store_product_id`),
              CONSTRAINT `d4store_pack_product_ibfk_pack` FOREIGN KEY (`pack_id`) REFERENCES `d4store_packs` (`id`),
              CONSTRAINT `d4store_pack_product_ibfk_store_product` FOREIGN KEY (`store_product_id`) REFERENCES `d4store_store_product` (`id`)
            ) ENGINE=InnoDB  DEFAULT CHARSET=latin1
                    
        ');
    }

    public function safeDown() {
        echo "m220105_131413_d4yii2_d4store_create_pack_product cannot be reverted.\n";
        return false;
    }
}
