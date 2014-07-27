<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Adam
 * Date: 15.06.13
 * Time: 13:38
 * To change this template use File | Settings | File Templates.
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

$sql = 'CREATE TABLE '. $this->getTable('cs_sare_email').'(
	    `id` BIGINT UNSIGNED NOT NULL,
	    `email` VARCHAR(255) NOT NULL,
	    `created_at` DATETIME NOT NULL,
	    PRIMARY KEY (`id`),
	    INDEX `email` (`id`)
        )   COMMENT= "Table responsible for holding email / key at SARE"
            COLLATE="latin1_swedish_ci"
            ENGINE=InnoDB;';

$installer->run($sql);
$installer->run('ALTER TABLE '.$this->getTable('cs_sare_email').' CHANGE `id` `id` INT(32) UNSIGNED AUTO_INCREMENT');
$installer->run('ALTER TABLE '.$this->getTable('cs_sare_email').' ADD COLUMN `mkey` VARCHAR(32) NULL AFTER `email`;');

$installer->endSetup();