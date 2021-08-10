<?php

use yii\db\Migration;

class m210810_080707_action_add_IsActive  extends Migration {

    public function safeUp() { 
        $this->execute('
            ALTER TABLE `d4store_action`
              ADD COLUMN `is_active` ENUM (\'Yes\', \'Not\') DEFAULT \'Yes\' NOT NULL COMMENT \'Is Active\' AFTER `stack_id`;
        ');
        $this->execute('
            ALTER TABLE `d4store_action`
              CHANGE `ref_model_id` `ref_model_id` TINYINT (3) UNSIGNED NULL,
              CHANGE `ref_model_record_id` `ref_model_record_id` INT (10) UNSIGNED NULL;
        ');
        $this->execute('
            ALTER TABLE `d4store_action` CHANGE `time` `time` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL; 
        ');
    }

    public function safeDown() {
        echo "m210810_080707_action_add_IsActive cannot be reverted.\n";
        return false;
    }
}
