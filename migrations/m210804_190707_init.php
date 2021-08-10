<?php

use yii\db\Migration;

class m210804_190707_init  extends Migration {

    public function safeUp() { 
        $this->execute('
            CREATE TABLE `d4store_store_product`(
            	`id` int(10) unsigned NOT NULL  auto_increment ,
            	`product_id` smallint(10) unsigned NOT NULL  ,
            	`qnt` decimal(13,3) unsigned NULL  ,
            	`remain_qnt` decimal(13,3) unsigned NULL  ,
            	`reserved_qnt` decimal(13,3) unsigned NULL  ,
            	PRIMARY KEY (`id`) ,
            	KEY `d3store_store_product_ibfk_product`(`product_id`) ,
            	CONSTRAINT `d4store_store_product_ibfk_product`
            	FOREIGN KEY (`product_id`) REFERENCES `d3product_product` (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=\'latin1\' COLLATE=\'latin1_swedish_ci\';
        ');

        $this->execute('
            CREATE TABLE `d4store_store`(
                `id` smallint(5) unsigned NOT NULL  auto_increment , 
                `company_id` smallint(5) unsigned NOT NULL  , 
                `name` varchar(50) COLLATE utf8_general_ci NULL  COMMENT \'Store Name\' , 
                `address` varchar(255) COLLATE utf8_general_ci NULL  COMMENT \'Store Address\' , 
                `active` tinyint(4) NULL  DEFAULT 1 COMMENT \'Active\' , 
                PRIMARY KEY (`id`) , 
                KEY `sys_company_id`(`company_id`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=\'utf8\' COLLATE=\'utf8_general_ci\';`        
        ');

        $this->execute('
            CREATE TABLE `d4store_stack`(
                `id` smallint(5) unsigned NOT NULL  auto_increment , 
                `store_id` smallint(5) unsigned NOT NULL  COMMENT \'Store\' , 
                `name` varchar(255) COLLATE utf8_general_ci NULL  COMMENT \'Stack name\' , 
                `notes` text COLLATE utf8_general_ci NULL  COMMENT \'Notes\' , 
                `active` tinyint(3) unsigned NOT NULL  DEFAULT 1 COMMENT \'Active\' , 
                PRIMARY KEY (`id`) , 
                KEY `store_id`(`store_id`) , 
                CONSTRAINT `d4store_stack_ibfk_store` 
                FOREIGN KEY (`store_id`) REFERENCES `d4store_store` (`id`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=\'utf8\' COLLATE=\'utf8_general_ci\';        
        ');

        $this->execute('
            CREATE TABLE `d4store_action`(
                `id` int(10) unsigned NOT NULL  auto_increment , 
                `store_product_id` int(10) unsigned NOT NULL  , 
                `type` enum(\'In\',\'Out\',\'To Process\',\'From Process\',\'Move\',\'Reservation\') COLLATE latin1_swedish_ci NOT NULL  , 
                `stack_id` smallint(5) unsigned NULL  , 
                `time` timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL, 
                `qnt` decimal(13,3) unsigned NOT NULL  , 
                `ref_model_id` tinyint(3) unsigned NOT NULL  , 
                `ref_model_record_id` int(10) unsigned NOT NULL  , 
                PRIMARY KEY (`id`) , 
                KEY `d3store_action_ibfk_store_product`(`store_product_id`) , 
                KEY `d3store_action_ibfk_stack`(`stack_id`) , 
                KEY `d3store_action_ibfk_ref_model`(`ref_model_id`) , 
                CONSTRAINT `d4store_action_ibfk_ref_model` 
                FOREIGN KEY (`ref_model_id`) REFERENCES `sys_models` (`id`) , 
                CONSTRAINT `d4store_action_ibfk_stack` 
                FOREIGN KEY (`stack_id`) REFERENCES `d4store_stack` (`id`) , 
                CONSTRAINT `d4store_action_ibfk_store_product` 
                FOREIGN KEY (`store_product_id`) REFERENCES `d4store_store_product` (`id`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=\'latin1\' COLLATE=\'latin1_swedish_ci\';        
        ');

        $this->execute('
            CREATE TABLE `d4store_action_ref`(
                `id` int(10) unsigned NOT NULL  auto_increment , 
                `action_id` int(10) unsigned NOT NULL  , 
                `model_id` tinyint(3) unsigned NOT NULL  , 
                `model_record_id` int(10) unsigned NOT NULL  , 
                PRIMARY KEY (`id`) , 
                KEY `d3store_action_ref_ibfk_action`(`action_id`) , 
                KEY `d3store_action_ref_ibfk_model`(`model_id`) , 
                CONSTRAINT `d4store_action_ref_ibfk_action` 
                FOREIGN KEY (`action_id`) REFERENCES `d4store_action` (`id`) , 
                CONSTRAINT `d4store_action_ref_ibfk_model` 
                FOREIGN KEY (`model_id`) REFERENCES `sys_models` (`id`) 
            ) ENGINE=InnoDB DEFAULT CHARSET=\'latin1\' COLLATE=\'latin1_swedish_ci\';        
        ');
    }

    public function safeDown() {
        echo "m210804_190707_init cannot be reverted.\n";
        return false;
    }
}
