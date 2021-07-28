/*
SQLyog Ultimate v13.1.8 (64 bit)
MySQL - 10.1.37-MariaDB : Database - aluksne
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Table structure for table `d4store_action` */

DROP TABLE IF EXISTS `d4store_action`;

CREATE TABLE `d4store_action` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_product_id` int(10) unsigned NOT NULL,
  `type` enum('In','Out','To Process','From Process','Move','Reservation') NOT NULL,
  `stack_id` smallint(5) unsigned DEFAULT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `qnt` decimal(13,3) unsigned NOT NULL,
  `ref_model_id` tinyint(3) unsigned NOT NULL,
  `ref_model_record_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `d3store_action_ibfk_store_product` (`store_product_id`),
  KEY `d3store_action_ibfk_stack` (`stack_id`),
  KEY `d3store_action_ibfk_ref_model` (`ref_model_id`),
  CONSTRAINT `d4store_action_ibfk_ref_model` FOREIGN KEY (`ref_model_id`) REFERENCES `sys_models` (`id`),
  CONSTRAINT `d4store_action_ibfk_stack` FOREIGN KEY (`stack_id`) REFERENCES `d4store_stack` (`id`),
  CONSTRAINT `d4store_action_ibfk_store_product` FOREIGN KEY (`store_product_id`) REFERENCES `d4store_store_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `d4store_action_ref` */

DROP TABLE IF EXISTS `d4store_action_ref`;

CREATE TABLE `d4store_action_ref` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `action_id` int(10) unsigned NOT NULL,
  `model_id` tinyint(3) unsigned NOT NULL,
  `model_record_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `d3store_action_ref_ibfk_action` (`action_id`),
  KEY `d3store_action_ref_ibfk_model` (`model_id`),
  CONSTRAINT `d4store_action_ref_ibfk_action` FOREIGN KEY (`action_id`) REFERENCES `d4store_action` (`id`),
  CONSTRAINT `d4store_action_ref_ibfk_model` FOREIGN KEY (`model_id`) REFERENCES `sys_models` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Table structure for table `d4store_stack` */

DROP TABLE IF EXISTS `d4store_stack`;

CREATE TABLE `d4store_stack` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` smallint(5) unsigned NOT NULL COMMENT 'Store',
  `name` varchar(255) DEFAULT NULL COMMENT 'Stack name',
  `notes` text COMMENT 'Notes',
  `active` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT 'Active',
  PRIMARY KEY (`id`),
  KEY `store_id` (`store_id`),
  CONSTRAINT `d4store_stack_ibfk_store` FOREIGN KEY (`store_id`) REFERENCES `d4store_store` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `d4store_store` */

DROP TABLE IF EXISTS `d4store_store`;

CREATE TABLE `d4store_store` (
  `id` smallint(5) unsigned NOT NULL AUTO_INCREMENT,
  `company_id` smallint(5) unsigned NOT NULL,
  `name` varchar(50) DEFAULT NULL COMMENT 'Store Name',
  `address` varchar(255) DEFAULT NULL COMMENT 'Store Address',
  `active` tinyint(4) DEFAULT '1' COMMENT 'Active',
  PRIMARY KEY (`id`),
  KEY `sys_company_id` (`company_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Table structure for table `d4store_store_product` */

DROP TABLE IF EXISTS `d4store_store_product`;

CREATE TABLE `d4store_store_product` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` smallint(10) unsigned NOT NULL,
  `qnt` decimal(13,3) unsigned DEFAULT NULL,
  `remain_qnt` decimal(13,3) unsigned DEFAULT NULL,
  `reserved_qnt` decimal(13,3) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `d3store_store_product_ibfk_product` (`product_id`),
  CONSTRAINT `d4store_store_product_ibfk_product` FOREIGN KEY (`product_id`) REFERENCES `d3product_product` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
