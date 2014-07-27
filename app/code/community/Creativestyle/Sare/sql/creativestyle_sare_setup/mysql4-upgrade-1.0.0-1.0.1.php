<?php
$installer = $this;

$installer->startSetup();


$sql = "INSERT INTO ".$this->getTable('core_translate')." (`string`, `store_id`, `translate`, `locale`) VALUES ('Mage_Adminhtml::<div class=\"sare-settings-headline\"> integration</div>', 0, '<div class=\"sare-settings-headline\"> integracja</div>', 'pl_PL');";

$installer->run($sql);

$installer->endSetup();