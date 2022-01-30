<?php

use yii\db\Migration;

class m220128_142955_d4yii2_d4store_store_product_add_code  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d4store_store_product`
              ADD COLUMN `code` VARCHAR (50) NULL AFTER `type`;
            
                    
        ');
    }

    public function safeDown() {
        echo "m220128_142955_d4yii2_d4store_store_product_add_code cannot be reverted.\n";
        return false;
    }
}
