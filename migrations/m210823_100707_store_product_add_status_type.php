<?php

use yii\db\Migration;

class m210823_100707_store_product_add_status_type  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d4store_store_product`
              ADD COLUMN `status` ENUM (
                \'Plan\',
                \'In Process\',
                \'Active\',
                \'Closed\'
              ) DEFAULT \'Active\' NULL AFTER `reserved_qnt`,
              ADD COLUMN `type` ENUM (\'Regular\', \'In Out\') DEFAULT \'Regular\' NULL AFTER `status`;        
        ');
    }

    public function safeDown() {
        echo "m210823_100707_store_product_add_status_type cannot be reverted.\n";
        return false;
    }
}
