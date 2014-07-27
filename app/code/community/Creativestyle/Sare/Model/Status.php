<?php
class Creativestyle_Sare_Model_Status{
    public function toOptionArray()
    {
        // This is list of statuses available in SARE with corresponding labels
        // Source: http://dev.sare.pl/sare-api/rest-api/aneks/
        return array(
            2=>Mage::helper('sare')->__('Removed by SARE operator'),
            3=>Mage::helper('sare')->__('Willing to sign off'),
            4=>Mage::helper('sare')->__('Willing to sign off (by link)'),
            5=>Mage::helper('sare')->__('Not confirmed, confirmation email not sent'),
            6=>Mage::helper('sare')->__('Saved, waiting to be confirmed'),
            7=>Mage::helper('sare')->__('Subscriber blocked'),
            8=>Mage::helper('sare')->__('Saved and confirmed')
        );
    }
}