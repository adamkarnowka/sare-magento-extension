<?php
class Creativestyle_Sare_Model_Email extends Mage_Core_Model_Abstract
{
    public function __construct() {
        $this->_init('sare/email', 'id');
    }

    public function loadByAttribute($attributeValue, $attributeCode){
        $collection = Mage::getModel('creativestyle_sare/email')->getCollection()->addFieldToFilter($attributeCode, array('eq'=>$attributeValue));

        if($collection->getSize()>0){
            return $collection->getFirstItem();
        }
        return false;
    }
}